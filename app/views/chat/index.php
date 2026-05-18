<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div x-data="chatApp()" x-init="initChat()" class="container mx-auto px-4 mt-8 pb-12 flex h-[80vh]">
    <!-- Conversation List (Sidebar) -->
    <aside class="w-full md:w-1/3 bg-white border-r rounded-l-lg overflow-y-auto">
        <div class="p-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Messages</h2>
        </div>

        <ul>
            <template x-for="conv in conversations" :key="conv.id">
                <li @click="selectConversation(conv)"
                    :class="{'bg-indigo-50 border-l-4 border-indigo-600': currentChat && currentChat.ad_id == conv.ad_id && currentChat.other_user_id == conv.other_user_id, 'hover:bg-gray-50': !currentChat || currentChat.ad_id != conv.ad_id}"
                    class="p-4 cursor-pointer transition flex items-center">
                    <div class="relative w-12 h-12 flex-shrink-0">
                        <!-- Avatar Placeholder -->
                        <div class="w-full h-full bg-gray-300 rounded-full flex items-center justify-center text-white text-lg font-bold uppercase"
                            x-text="conv.other_user_name[0]"></div>
                        <template x-if="conv.unread_count > 0">
                            <span
                                class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white"
                                x-text="conv.unread_count"></span>
                        </template>
                    </div>
                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between">
                            <h4 class="font-bold text-gray-800 truncate" x-text="conv.other_user_name"></h4>
                            <span class="text-xs text-gray-500" x-text="formatDate(conv.created_at)"></span>
                        </div>
                        <p class="text-sm text-gray-600 truncate">
                            <span class="font-medium text-indigo-600" x-text="conv.ad_title"></span>:
                            <span x-text="conv.message"></span>
                        </p>
                    </div>
                </li>
            </template>
            <template x-if="conversations.length === 0">
                <li class="p-8 text-center text-gray-500">No conversations yet.</li>
            </template>
        </ul>
    </aside>

    <!-- Chat Window -->
    <main class="w-full md:w-2/3 bg-gray-50 flex flex-col rounded-r-lg">
        <template x-if="!currentChat">
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                    </path>
                </svg>
                <p class="text-lg">Select a conversation to start chatting</p>
            </div>
        </template>

        <template x-if="currentChat">
            <div class="flex-1 flex flex-col h-full">
                <!-- Chat Header -->
                <div class="bg-white p-4 border-b flex justify-between items-center shadow-sm z-10">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold mr-3"
                            x-text="currentChat.other_user_name[0]"></div>
                        <div>
                            <h3 class="font-bold text-gray-800" x-text="currentChat.other_user_name"></h3>
                            <p class="text-xs text-gray-500">Ad: <span x-text="currentChat.ad_title"></span></p>
                        </div>
                    </div>
                    <a :href="'<?php echo URL_ROOT; ?>/listings/' + currentChat.ad_id"
                        class="text-sm text-indigo-600 hover:text-indigo-800">View Ad</a>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                    <template x-for="msg in currentMessages" :key="msg.id">
                        <div class="flex" :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">
                            <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow-sm"
                                :class="msg.sender_id == currentUserId ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'">
                                <p x-text="msg.message"></p>
                                <span class="text-xs block mt-1 opacity-75"
                                    :class="msg.sender_id == currentUserId ? 'text-indigo-200 text-right' : 'text-gray-400'"
                                    x-text="formatTime(msg.created_at)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input Area -->
                <div class="bg-white p-4 border-t">
                    <form @submit.prevent="sendMessage" class="flex items-center">
                        <input x-model="newMessage" type="text" placeholder="Type a message..."
                            class="flex-1 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 mr-2">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-6 py-3 font-semibold shadow transition disabled:opacity-50"
                            :disabled="!newMessage.trim()">
                            <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </main>
</div>

<script>
    function chatApp() {
        return {
            conversations: [],
            currentChat: <?php echo isset($data['current_chat']) && $data['current_chat'] ? json_encode($data['current_chat']) : 'null'; ?>,
            currentMessages: [],
            currentUserId: <?php echo $_SESSION['user_id']; ?>,
            newMessage: '',
            pollInterval: null,

            initChat() {
                // If we have initial chat, set messages
                if (this.currentChat) {
                    this.currentMessages = this.currentChat.messages || [];
                    this.$nextTick(() => {
                        const container = document.getElementById('messages-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    });
                }

                this.fetchConversations();

                // Poll for everything every 3 seconds
                this.pollInterval = setInterval(() => {
                    this.fetchConversations();
                    // Only poll messages if we have an active chat
                    if (this.currentChat) {
                        this.fetchMessages();
                    }
                }, 3000);
            },

            fetchConversations() {
                fetch('<?php echo URL_ROOT; ?>/chat/poll')
                    .then(res => res.json())
                    .then(data => {
                        if (data.conversations) {
                            this.conversations = data.conversations;
                        }
                    });
            },

            selectConversation(conv) {
                this.currentChat = {
                    ad_id: conv.ad_id,
                    other_user_id: conv.other_user_id,
                    other_user_name: conv.other_user_name,
                    ad_title: conv.ad_title
                };
                this.fetchMessages(true);
            },

            fetchMessages(scrollToBottom = false) {
                if (!this.currentChat) return;

                const params = new URLSearchParams({
                    ad_id: this.currentChat.ad_id,
                    other_user_id: this.currentChat.other_user_id
                }).toString();

                fetch('<?php echo URL_ROOT; ?>/chat/poll?' + params)
                    .then(res => res.json())
                    .then(data => {
                        if (data.messages) {
                            this.currentMessages = data.messages;
                            if (scrollToBottom) {
                                this.$nextTick(() => {
                                    const container = document.getElementById('messages-container');
                                    if (container) container.scrollTop = container.scrollHeight;
                                });
                            }
                        }
                    });
            },

            sendMessage() {
                if (!this.newMessage.trim() || !this.currentChat) return;

                const payload = {
                    ad_id: this.currentChat.ad_id,
                    receiver_id: this.currentChat.other_user_id,
                    message: this.newMessage
                };

                fetch('<?php echo URL_ROOT; ?>/chat/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.newMessage = '';
                            this.fetchMessages(true);
                            this.fetchConversations(); // Update list immediately (snippet)
                        }
                    });
            },

            formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString();
            },

            formatTime(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>