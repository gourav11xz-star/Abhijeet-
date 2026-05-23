<div x-data="chatPopup()" x-init="initChat()" class="fixed bottom-4 right-4 z-50 flex flex-col items-end"
    @open-chat.window="openChat($event.detail.adId, $event.detail.receiverId)" style="display: none;" x-show="true">

    <!-- Chat Window -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed inset-0 w-full h-full md:static md:w-96 md:h-[500px] bg-white md:rounded-t-2xl shadow-2xl md:border border-gray-200 flex flex-col overflow-hidden z-50 md:mb-4">

        <!-- Header -->
        <div
            class="bg-indigo-600 p-4 flex justify-between items-center text-white rounded-t-2xl shadow-md flex-shrink-0">
            <div class="flex items-center space-x-2">
                <!-- Back Button (only in active chat) -->
                <button x-show="hasActiveChat" @click="backToList" class="mr-1 hover:text-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <div x-show="hasActiveChat" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                        <span x-text="receiverName ? receiverName.charAt(0).toUpperCase() : 'C'"></span>
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-sm truncate w-32" x-text="receiverName || 'Chat'"></h3>
                        <p class="text-xs text-indigo-100 truncate w-32" x-show="adTitle" x-text="adTitle"></p>
                    </div>
                </div>
                <div x-show="!hasActiveChat" class="font-bold text-lg">
                    Messages
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button"
                    x-show="hasActiveChat"
                    @click="deleteConversation()"
                    title="Delete chat"
                    class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                    Delete
                </button>
                <button @click="minimize" class="hover:text-indigo-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                    </svg>
                </button>
                <button @click="close" class="hover:text-indigo-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Conversation List View -->
        <div x-show="!hasActiveChat" class="flex-grow overflow-y-auto bg-gray-50">
            <template x-for="conv in conversations" :key="conv.id">
                <div @click="openChat(conv.ad_id, conv.other_user_id)"
                    class="p-4 bg-white border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0">
                        <span x-text="conv.user_name.charAt(0).toUpperCase()"></span>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <h4 class="font-bold text-gray-900 text-sm truncate" x-text="conv.user_name"></h4>
                            <span class="text-xs text-gray-400 whitespace-nowrap"
                                x-text="formatTime(conv.created_at)"></span>
                        </div>
                        <p class="text-xs text-indigo-600 font-medium truncate mb-1" x-text="conv.ad_title"></p>
                        <p class="text-sm text-gray-500 truncate" x-text="conv.message"></p>
                    </div>
                </div>
            </template>
            <div x-show="conversations.length === 0 && !loading" class="text-center text-gray-500 text-sm mt-10 p-4">
                <p>No conversations yet.</p>
                <p class="mt-2 text-xs">Browse listings and chat with sellers!</p>
            </div>
            <div x-show="loading" class="flex justify-center py-10">
                <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Active Chat Messages Area -->
        <div x-show="hasActiveChat" class="flex-grow p-4 overflow-y-auto bg-gray-50 mb-2 space-y-4" id="chat-messages">
            <div x-show="loading" class="flex justify-center py-4">
                <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex" :class="msg.sender_id == userId ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-sm"
                        :class="msg.sender_id == userId ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'">
                        <p x-text="msg.message"></p>
                        <span class="text-[10px] block mt-1"
                            :class="msg.sender_id == userId ? 'text-indigo-200' : 'text-gray-400'"
                            x-text="formatTime(msg.created_at)"></span>
                    </div>
                </div>
            </template>
            <div x-show="messages.length === 0 && !loading" class="text-center text-gray-400 text-sm mt-10">
                <p>Start the conversation!</p>
            </div>
            <div x-show="isTyping" class="flex justify-start">
                <div
                    class="bg-gray-100 text-gray-500 rounded-2xl rounded-bl-none px-4 py-2 text-sm shadow-sm flex items-center space-x-1">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce delay-100"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce delay-200"></span>
                </div>
            </div>
        </div>

        <!-- Input Area (Only in Active Chat) -->
        <div x-show="hasActiveChat" class="p-3 bg-white border-t border-gray-100 flex-shrink-0">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="newMessage" placeholder="Type a message..."
                    class="flex-grow px-4 py-2 bg-gray-100 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                <button type="submit" :disabled="!newMessage.trim()"
                    class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Collapsed State (Floating Button) -->
    <div x-show="!isOpen" @click="isOpen = true; if(!hasActiveChat) fetchConversations()"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-50"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-indigo-600 text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center cursor-pointer hover:bg-indigo-700 hover:scale-105 transition-all group">
        <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
            </path>
        </svg>
        <span x-show="unreadCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white"
            x-text="unreadCount"></span>
    </div>

</div>
<script>
    function chatPopup() {
        return {
            isOpen: false,
            hasActiveChat: false,
            loading: false,
            userId: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>,
            adId: null,
            receiverId: null,
            receiverName: '',
            adTitle: '',
            messages: [],
            conversations: [],
            newMessage: '',
            isTyping: false,
            pollInterval: null,

            initChat() {
                const savedState = localStorage.getItem('chatPopupState');
                if (savedState) {
                    const state = JSON.parse(savedState);
                    if (state.hasActiveChat) {
                        this.adId = state.adId;
                        this.receiverId = state.receiverId;
                        this.receiverName = state.receiverName;
                        this.adTitle = state.adTitle;
                        this.hasActiveChat = true;
                        if (state.isOpen) this.openChat(this.adId, this.receiverId);
                    } else if (state.isOpen) {
                        this.isOpen = true;
                        this.fetchConversations();
                    }
                }
            },

            backToList() {
                this.hasActiveChat = false;
                this.stopPolling();
                this.adId = null;
                this.receiverId = null;
                this.fetchConversations();
                this.saveState();
            },

            async openChat(adId, receiverId) {
                if (!this.userId) {
                    window.location.href = '<?php echo URL_ROOT; ?>/login';
                    return;
                }

                // If opened with null (from navbar), just show list
                if (!adId || !receiverId) {
                    this.isOpen = true;
                    this.hasActiveChat = false;
                    this.fetchConversations();
                    this.saveState();
                    return;
                }

                this.adId = adId;
                this.receiverId = receiverId;
                this.isOpen = true;
                this.hasActiveChat = true;
                this.fetchMessages();
                this.startPolling();
                this.saveState();
            },

            minimize() {
                this.isOpen = false;
                this.saveState();
            },

            close() {
                this.isOpen = false;
                this.hasActiveChat = false;
                this.stopPolling();
                localStorage.removeItem('chatPopupState');
            },

            saveState() {
                localStorage.setItem('chatPopupState', JSON.stringify({
                    isOpen: this.isOpen,
                    hasActiveChat: this.hasActiveChat,
                    adId: this.adId,
                    receiverId: this.receiverId,
                    receiverName: this.receiverName,
                    adTitle: this.adTitle
                }));
            },

            async fetchConversations() {
                this.loading = true;
                try {
                    const response = await fetch('<?php echo URL_ROOT; ?>/chat/api_get_conversations');
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.conversations = data.conversations;
                    }
                } catch (error) {
                    console.error('Error fetching conversations:', error);
                }
                this.loading = false;
            },

            async fetchMessages() {
                if (!this.adId || !this.receiverId) return;

                // Only show loading on initial fetch if empty
                if (this.messages.length === 0) this.loading = true;

                const params = new URLSearchParams({
                    ad_id: this.adId,
                    receiver_id: this.receiverId
                });

                try {
                    const response = await fetch('<?php echo URL_ROOT; ?>/chat/api_get_messages?' + params.toString());
                    const data = await response.json();

                    if (data.status === 'success') {
                        const prevLength = this.messages.length;
                        this.messages = data.messages;
                        this.receiverName = data.receiver_name;
                        this.adTitle = data.ad_title;

                        // If new messages arrived (e.g. from bot), stop typing indicator
                        if (this.messages.length > prevLength && this.isTyping) {
                            this.isTyping = false;
                            this.scrollToBottom();
                        } else if (this.messages.length === 0) {
                            // Initial load or empty
                        } else if (prevLength === 0) {
                            this.scrollToBottom();
                        }
                    }
                } catch (error) {
                    console.error('Error fetching messages:', error);
                }
                this.loading = false;
            },

            
            async deleteConversation() {
                if (!this.adId || !this.receiverId) {
                    alert('No active chat selected.');
                    return;
                }

                if (!confirm('Delete this chat for both sides?')) {
                    return;
                }

                const formData = new FormData();
                formData.append('ad_id', this.adId);
                formData.append('other_user_id', this.receiverId);

                try {
                    const response = await fetch('<?php echo URL_ROOT; ?>/chat/delete_conversation', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        this.messages = [];
                        this.hasActiveChat = false;
                        this.adId = null;
                        this.receiverId = null;
                        this.receiverName = '';
                        this.adTitle = '';
                        localStorage.removeItem('chatPopupState');
                        this.fetchConversations();
                        alert('Chat deleted for both sides.');
                    } else {
                        alert(data.message || 'Could not delete chat.');
                    }
                } catch (error) {
                    alert('Delete failed. Please try again.');
                }
            },

async sendMessage() {
                console.log('Attempting to send message...');
                if (!this.newMessage.trim()) {
                    console.log('Message is empty');
                    return;
                }

                if (!this.adId || !this.receiverId) {
                    alert('Error: Missing chat context (Ad ID or User ID). Please try reopening the chat.');
                    console.error('Missing context:', this.adId, this.receiverId);
                    return;
                }

                const formData = new FormData();
                formData.append('ad_id', this.adId);
                formData.append('receiver_id', this.receiverId);
                formData.append('message', this.newMessage);

                // Start typing simulation immediately
                this.isTyping = true;
                this.scrollToBottom();

                try {
                    console.log('Posting to API...');
                    const response = await fetch('<?php echo URL_ROOT; ?>/chat/api_send_message', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        if (data.status === 'success') {
                            console.log('Message sent successfully');
                            this.newMessage = '';
                            this.fetchMessages();
                        } else {
                            this.isTyping = false;
                            console.error('Send failed (API):', data.message);
                            alert('Failed to send message: ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        this.isTyping = false;
                        console.error('Invalid JSON response:', text);
                        alert('Server error: Invalid response format.');
                    }

                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Network error: ' + error.message);
                }
            },

            startPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.pollInterval = setInterval(() => {
                    if (this.isOpen && this.hasActiveChat) this.fetchMessages();
                }, 3000);
            },

            stopPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('chat-messages');
                    if (container) container.scrollTop = container.scrollHeight;
                });
            },

            formatTime(dateString) {
                const date = new Date(dateString);
                // Check if today
                const now = new Date();
                if (date.toDateString() === now.toDateString()) {
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
                return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
            }
        }
    }
</script>