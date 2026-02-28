<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AiLog.php';

class TutorialController
{
    private $aiLogModel;

    public function __construct()
    {
        $this->aiLogModel = new AiLog();
    }

    public function index()
    {
        require_once __DIR__ . '/../views/tutorial/index.php';
    }

    public function ask()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $type = sanitize($_POST['type'] ?? '');
        $question = sanitize($_POST['question'] ?? '');
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($question)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập câu hỏi']);
            return;
        }

        $answer = $this->generateAiAnswer($type, $question);

        // Log the interaction
        $this->aiLogModel->create($userId, $question, $answer, $type);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'answer' => $answer
        ]);
    }

    private function generateAiAnswer($type, $question)
    {
        $apiKey = getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            return "Xin lỗi, hệ thống chưa được cấu hình API Key. Vui lòng liên hệ quản trị viên.";
        }

        $systemPrompts = [
            'play' => "Bạn là trợ lý AI của cửa hàng Game Store. Hãy hướng dẫn người dùng cách chơi game, cách cài đặt, phím tắt và các mẹo cơ bản. Trả lời bằng tiếng Việt, thân thiện và ngắn gọn.",
            'suggest' => "Bạn là chuyên gia gợi ý game của Game Store. Dựa trên sở thích của người dùng, hãy gợi ý những tựa game phù hợp nhất đang có trên thị trường hoặc trong cửa hàng. Trả lời bằng tiếng Việt, lôi cuốn.",
            'guide' => "Bạn là nhân viên hỗ trợ khách hàng của Game Store. Hãy hướng dẫn người dùng cách sử dụng website (mua game, giỏ hàng, thanh toán, quản lý tài khoản). Website hỗ trợ MoMo, ZaloPay, Chuyển khoản. Trả lời bằng tiếng Việt, chuyên nghiệp.",
            'error' => "Bạn là kỹ thuật viên của Game Store. Hãy giải thích và hướng dẫn khắc phục các lỗi thường gặp về kỹ thuật, đăng nhập, nạp tiền hoặc tải game. Trả lời bằng tiếng Việt, bình tĩnh và chi tiết."
        ];

        $systemPrompt = $systemPrompts[$type] ?? "Bạn là trợ lý AI hữu ích của Game Store.";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $question]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return "Lỗi kết nối AI: " . $error;
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        }

        return "Xin lỗi, AI hiện không thể trả lời. Vui lòng thử lại sau. (Code: " . ($result['error']['code'] ?? 'unknown') . ")";
    }
}
?>