(function ($) {


    $(document).ready(function () {

        $('body').addClass('stm_lms_chat_page');

        new Vue({
            el: '#stm_lms_chat',
            data: function () {
                return {
                    conversations: [],
                    conversation: '',
                    myMessage: '',
                    loading: false,
                    updating: false,
                }
            },
            mounted: function () {
                this.getConversation(false);
            },
            methods: {
                updateConversation() {
                    var vm = this;
                    vm.updating = true;
                    vm.getMessages(vm.conversations[vm.conversation]['conversation_info']['conversation_id'], true);
                },
                getConversation(update) {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_conversations&nonce=' + stm_lms_nonces['stm_lms_get_user_conversations'];

                    this.$http.get(url).then(function (response) {

                        response['body'].forEach(function (value, index) {
                            vm.conversations.push(value);
                        });

                        if (vm.conversations.length) {
                            vm.conversation = 0;
                        }

                    });
                },
                getMessages(conversation_id, update, just_send) {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_messages&nonce='
                        + stm_lms_nonces['stm_lms_get_user_messages']
                        + '&id=' + conversation_id
                        + '&just_send=' + just_send;

                    if (typeof vm.conversations[vm.conversation]['messages'] !== 'undefined' && !update) {
                        vm.scrollMessagesBottom();
                        return false;
                    }


                    if (!vm.conversations[vm.conversation].length) {
                        this.$http.get(url).then(function (response) {
                            vm.$set(vm.conversations[vm.conversation], 'messages', response.body['messages']);
                            vm.scrollMessagesBottom();
                        });
                    }
                },
                changeChat(index) {
                    this.conversation = index;
                },
                scrollMessagesBottom() {
                    this.updating = false;
                    this.$nextTick(() => {
                        var container = this.$el.querySelector("#stm_lms_chat_messages");
                        container.scrollTop = container.scrollHeight;
                    });
                },
                sendMessage() {
                    var vm = this;
                    vm.loading = true;

                    var user_to = (vm.conversations[vm.conversation]['companion']['id']);

                    if (vm.myMessage) {
                        var url = stm_lms_ajaxurl + '?action=stm_lms_send_message&nonce=' + stm_lms_nonces['stm_lms_send_message'] + '&to=' + user_to + '&message=' + vm.myMessage;
                        vm.loading = true;

                        this.$http.get(url).then(function (response) {
                            vm.getMessages(vm.conversations[vm.conversation]['conversation_info']['conversation_id'], true, true);
                            vm.loading = false;
                            vm['myMessage'] = '';
                        });
                    }
                }
            },
            watch: {
                conversation: function (conversation_id) {
                    conversation_id = this.conversations[conversation_id]['conversation_info']['conversation_id'];
                    this.getMessages(conversation_id, false);
                }
            }
        });
    });

})(jQuery);
