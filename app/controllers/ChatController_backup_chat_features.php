<?php
class ChatController extends Controller
{
    private $messageModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('login');
        }
        $this->messageModel = $this->model('Message');
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $conversations = $this->messageModel->getConversations($userId); // Simplified method, need implementation

        // This index will probably list all conversations.
        // We'll return a view with conversations data.
        $data = [
            'conversations' => $conversations,
            'current_chat' => null
        ];

        $this->view('chat/index', $data);
    }

    // Show a specific conversation
    public function conversation($adId, $otherUserId)
    {
        $userId = $_SESSION['user_id'];

        // Mark as read first
        $this->messageModel->markAsRead($userId, $otherUserId, $adId);

        $messages = $this->messageModel->getConversationMessages($userId, $otherUserId, $adId);
        $conversations = $this->messageModel->getConversations($userId);

        // Fetch details for the current chat context
        $adModel = $this->model('Ad');
        $userModel = $this->model('User');

        $ad = $adModel->getAdById($adId);
        $otherUser = $userModel->getUserById($otherUserId);

        $currentChat = [
            'ad_id' => $adId,
            'other_user_id' => $otherUserId,
            'other_user_name' => $otherUser ? $otherUser->name : 'Unknown User',
            'ad_title' => $ad ? $ad->title : 'Unknown Ad',
            'messages' => $messages
        ];

        $data = [
            'conversations' => $conversations,
            'current_chat' => $currentChat
        ];

        $this->view('chat/index', $data);
    }

    // AJAX: Get unread count
    public function unread_count()
    {
        $count = $this->messageModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
    }

    // AJAX: Poll for new messages 
    public function poll()
    {
        $userId = $_SESSION['user_id'];
        $adId = $_GET['ad_id'] ?? null;
        $otherUserId = $_GET['other_user_id'] ?? null;

        if ($adId && $otherUserId) {
            // In polling, we only want *new* messages since last check?
            // Or just return last 20 messages?
            // Simplest approach: Return latest messages.
            // Ideally we pass a timestamp `after_id`
            $messages = $this->messageModel->getConversationMessages($userId, $otherUserId, $adId);

            // Mark as read if user is "in" the chat? 
            // Maybe explicitly mark read on poll if window is active.
            $this->messageModel->markAsRead($userId, $otherUserId, $adId);

            echo json_encode(['messages' => $messages]);
        } else {
            // Just return conversations/unread list
            $conversations = $this->messageModel->getConversations($userId);
            echo json_encode(['conversations' => $conversations]);
        }
    }

    // AJAX: Send message
    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verify CSRF ? 
            // In AJAX, we send CSRF token as header or data.
            $data = json_decode(file_get_contents('php://input'), true);

            // Check spam flood? 
            // (Simplified version: just insert)

            $msgData = [
                'sender_id' => $_SESSION['user_id'],
                'receiver_id' => $data['receiver_id'],
                'ad_id' => $data['ad_id'],
                'message' => sanitize_input($data['message'])
            ];

            if (!empty($msgData['message'])) {
                $msgId = $this->messageModel->sendMessage($msgData);
                if ($msgId) {
                    echo json_encode(['status' => 'success', 'message_id' => $msgId]);
                } else {
                    echo json_encode(['status' => 'error']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Empty message']);
            }
        }
    }
    // API: Get Messages for Popup
    public function api_get_messages()
    {
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $adId = isset($_GET['ad_id']) ? $_GET['ad_id'] : null;
        $receiverId = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : null;

        if (!$adId || !$receiverId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
            return;
        }

        // Mark messages as read
        $this->messageModel->markAsRead($_SESSION['user_id'], $receiverId, $adId);

        // Use existing model method to get messages
        $messages = $this->messageModel->getConversationMessages($_SESSION['user_id'], $receiverId, $adId);

        // Fetch User and Ad details
        $userModel = $this->model('User');
        $receiver = $userModel->getUserById($receiverId);

        $adModel = $this->model('Ad');
        $ad = $adModel->getAdById($adId);

        echo json_encode([
            'status' => 'success',
            'messages' => $messages,
            'receiver_name' => $receiver ? $receiver->name : 'Unknown User',
            'ad_title' => $ad ? $ad->title : 'Unknown Ad'
        ]);
    }

    // API: Send Message for Popup
    public function api_send_message()
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $adId = (int)($_POST['ad_id'] ?? 0);
        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $senderId = (int)$_SESSION['user_id'];
        $message = trim($_POST['message'] ?? '');

        if ($adId <= 0 || $receiverId <= 0 || $message === '') {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
            return;
        }

        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $cols = $pdo->query("SHOW COLUMNS FROM messages")->fetchAll(PDO::FETCH_COLUMN);
            $data = [];

            if (in_array('ad_id', $cols)) $data['ad_id'] = $adId;
            if (in_array('sender_id', $cols)) $data['sender_id'] = $senderId;
            if (in_array('receiver_id', $cols)) $data['receiver_id'] = $receiverId;
            if (in_array('message', $cols)) $data['message'] = $message;
            if (in_array('content', $cols)) $data['content'] = $message;
            if (in_array('is_read', $cols)) $data['is_read'] = 0;
            if (in_array('created_at', $cols)) $data['created_at'] = date('Y-m-d H:i:s');
            if (in_array('updated_at', $cols)) $data['updated_at'] = date('Y-m-d H:i:s');

            $columns = array_keys($data);
            $placeholders = array_map(function($c) { return ':' . $c; }, $columns);

            $sql = "INSERT INTO messages (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            echo json_encode(['status' => 'success', 'message' => 'Message sent']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Auto-Bot Logic
    private function autoReply($originalSenderId, $originalReceiverId, $adId, $userMessage)
    {
        // define bot (the "receiver" of the original message becomes the sender)
        $botId = $originalReceiverId;
        $userId = $originalSenderId;

        // Initialize Ad Model
        $adModel = $this->model('Ad');
        $currentAd = $adModel->getAdById($adId);

        if (!$currentAd) {
            // If Ad is invalid, we can just skip context 1 and proceed to search or fallback
            // But context 1 relies on it. Let's initialize a dummy or skip.
            // Better to just set $currentAd to null and check before accessing properties.
        }

        $msgLower = strtolower($userMessage);
        $reply = "";

        // 1. Context: Current Ad Details
        if ($currentAd) {
            if (strpos($msgLower, 'price') !== false || strpos($msgLower, 'cost') !== false) {
                $reply = "The price for '" . $currentAd->title . "' is " . $currentAd->currency . " " . number_format($currentAd->price) . ".";
            } elseif (strpos($msgLower, 'location') !== false || strpos($msgLower, 'where') !== false) {
                $reply = "This item is located in " . $currentAd->city . ", " . $currentAd->state . ".";
            } elseif (strpos($msgLower, 'available') !== false) {
                $reply = "Yes, '" . $currentAd->title . "' is currently available!";
            } elseif (strpos($msgLower, 'details') !== false || strpos($msgLower, 'condition') !== false) {
                $reply = "It is listed as " . $currentAd->condition_type . ". Description: " . substr($currentAd->description, 0, 100) . "...";
            }
        }

        // 2. Context: Global Search & Advanced Filtering
        if (empty($reply)) {
            $filter = ['limit' => 3];
            $searchPerformed = false;

            // Extract Price "under 5000"
            if (preg_match('/(under|below|less than|cheaper than)\s+(\d+)/', $msgLower, $priceMatches)) {
                $filter['max_price'] = $priceMatches[2];
                $msgLower = str_replace($priceMatches[0], '', $msgLower); // Remove from search string
                $searchPerformed = true;
            }

            // Extract Location "in Mumbai"
            if (preg_match('/(in|at|near)\s+([a-z\s]+)/', $msgLower, $locMatches)) {
                $rawLoc = trim($locMatches[2]);
                $filter['location_text'] = $rawLoc;
                $msgLower = str_replace($locMatches[0], '', $msgLower);
                $searchPerformed = true;
            }

            // Clean up search term to find the "Object" (e.g. "car")
            // Remove common filler words
            $stopWords = ['i', 'want', 'need', 'looking', 'for', 'show', 'me', 'do', 'you', 'have', 'any', 'a', 'an', 'the', 'please', 'search', 'find'];
            $words = explode(' ', $msgLower);
            $keywords = [];
            foreach ($words as $word) {
                $word = trim($word, " \t\n\r\0\x0B?.!,");
                if (!empty($word) && !in_array($word, $stopWords)) {
                    $keywords[] = $word;
                }
            }

            if (!empty($keywords)) {
                $filter['search'] = implode(' ', $keywords);
                $searchPerformed = true;
            }

            if ($searchPerformed) {
                $results = $adModel->getAds($filter);

                if (!empty($results)) {
                    $count = count($results);
                    $reply = " $count " . ($count == 1 ? "item" : "items") . " matching your criteria:\n";
                    if (!empty($filter['max_price']))
                        $reply .= " (Price < " . $filter['max_price'] . ")";
                    if (!empty($filter['location_text']))
                        $reply .= " (in " . ucwords($filter['location_text']) . ")";
                    $reply .= "\n";

                    foreach ($results as $item) {
                        $reply .= "- " . $item->title . " (" . $item->currency . number_format($item->price) . ")\n";
                    }
                    $reply .= "";
                } else {
                    $reply = "I looked for items";
                    if (!empty($filter['search']))
                        $reply .= " matching '" . $filter['search'] . "'";
                    if (!empty($filter['max_price']))
                        $reply .= " under " . $filter['max_price'];
                    if (!empty($filter['location_text']))
                        $reply .= " in " . $filter['location_text'];
                    $reply .= ", but couldn't find any. ";
                }
            }
        }

        // 3. Fallback
        if (empty($reply)) {
            $reply = "Hi there! 👋 Thanks for reaching out. I can help you with the **price**, **location**, or **availability** of this item.\n\nLooking for something else? Just type what you need (e.g., 'search for laptops') and I'll see what we have!";
        }

        // Send the reply
        $botMessageData = [
            'sender_id' => $botId,
            'receiver_id' => $userId,
            'ad_id' => $adId,
            'message' => $reply
        ];

        $this->messageModel->sendMessage($botMessageData);
    }

    // API: Get Conversations for Popup
    public function api_get_conversations()
    {
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $conversations = $this->messageModel->getConversations($_SESSION['user_id']);
        echo json_encode(['status' => 'success', 'conversations' => $conversations]);
    }

    // API: Get Unread Count
    public function api_get_unread_count()
    {
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'error', 'count' => 0]);
            return;
        }

        $count = $this->messageModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['status' => 'success', 'count' => $count]);
    }
}
