<?php

class AiService
{
    private $apiKey;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY');
    }

    private function makeRequest($systemPrompt, $userPrompt, $temperature = 0.7)
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'API Key chưa được cấu hình.'];
        }

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => $temperature
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'message' => 'Lỗi kết nối AI: ' . $error];
        }
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['choices'][0]['message']['content'])) {
            return ['success' => true, 'content' => $result['choices'][0]['message']['content']];
        }

        $errorMessage = $result['error']['message'] ?? 'Lỗi không xác định từ AI';
        return ['success' => false, 'message' => 'AI Error: ' . $errorMessage];
    }

    public function generateGameInfo($gameName, $categoryList = '')
    {
        $systemPrompt = "Bạn là một chuyên gia về game. Hãy viết thông tin cho tựa game được yêu cầu. Trả về kết quả dưới định dạng JSON theo cấu trúc sau:
{
    \"description\": \"Mô tả chi tiết, hấp dẫn về game (khoảng 3-4 đoạn)\",
    \"minimum_requirements\": \"Thông tin cấu hình tối thiểu (OS, Processor, Memory, Graphics, Storage) dạng text, mỗi thông tin cách nhau bằng dấu phẩy hoặc |\",
    \"recommended_requirements\": \"Thông tin cấu hình đề nghị dạng text, mỗi thông tin cách nhau bằng dấu phẩy hoặc |\"
}
Chỉ trả về JSON hợp lệ, không kèm markdown hay văn bản nào khác. Nếu có danh sách thể loại, hãy dựa vào đó để viết mô tả phù hợp.";

        $userPrompt = "Tên game: $gameName\nThể loại: $categoryList";

        $result = $this->makeRequest($systemPrompt, $userPrompt, 0.7);
        if ($result['success']) {
            // Cố gắng parse JSON
            $content = trim($result['content']);
            if (strpos($content, '```json') === 0) {
                $content = substr($content, 7);
                $content = rtrim($content, '`');
            }
            $json = json_decode(trim($content), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return ['success' => true, 'data' => $json];
            } else {
                return ['success' => false, 'message' => 'Không thể parse kết quả từ AI: ' . json_last_error_msg()];
            }
        }

        return $result;
    }

    public function analyzeRevenue($revenueData)
    {
        $systemPrompt = "Bạn là một chuyên gia phân tích dữ liệu tài chính tài ba. Hãy phân tích các số liệu doanh thu của một cửa hàng game và đưa ra nhận xét, đánh giá về tình hình hoạt động, xu hướng và đề xuất chiến lược phát triển dưới dạng HTML. Cấu trúc html bằng các thẻ <h3>, <p>, <ul>, <li> hợp lý, không sử dụng markdown code block, chỉ nội dung HTML thô, để chèn trực tiếp vào div.";
        $userPrompt = "Dữ liệu doanh thu:\n$revenueData";

        $result = $this->makeRequest($systemPrompt, $userPrompt, 0.5);
        if ($result['success']) {
            $content = str_replace(['```html', '```'], '', $result['content']);
            return ['success' => true, 'data' => trim($content)];
        }
        return $result;
    }

    public function moderateReview($reviewContent, $rating = 0)
    {
        $systemPrompt = "Bạn là hệ thống kiểm duyệt nội dung của một cửa hàng game. Hãy phân tích đánh giá của người dùng xem có vi phạm tiêu chuẩn cộng đồng (chứa từ ngữ thô tục, xúc phạm, spam, quảng cáo rác) hay không.
Trả về một JSON duy nhất có cấu trúc:
{
    \"is_approved\": true/false,
    \"reason\": \"Lý do nếu không được duyệt, hoặc nhận xét ngắn gọn nếu được duyệt\",
    \"sentiment\": \"positive/neutral/negative\"
}";
        $userPrompt = "Số sao: $rating\nNội dung: $reviewContent";

        $result = $this->makeRequest($systemPrompt, $userPrompt, 0.3);
        if ($result['success']) {
            $content = trim($result['content']);
            if (strpos($content, '```json') === 0) {
                $content = substr($content, 7);
                $content = rtrim($content, '`');
            }
            $json = json_decode(trim($content), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return ['success' => true, 'data' => $json];
            } else {
                return ['success' => false, 'message' => 'JSON Error'];
            }
        }
        return $result;
    }
}
?>