<?php  namespace Larabook\Conversations; 

use Illuminate\Support\Facades\Auth;

class PreviewRepository {
    /**
     * Get Previews of the conversations
     *
     * @return array
     */
    public function getPreviewsOf($convs)
    {
        $previews= [];
        foreach($convs as $conv)
        {
            $previews[] = $this->makePreviewFor($conv);
        }

        return $previews;
    }


    /**
     * Make a preview for the conversation
     *
     * @param Conversation $conv
     * @return \Larabook\Conversations\ConversationPreview
     */
    public function makePreviewFor(Conversation $conv)
    {
        //first() method because in the messages relationship we get the messages with latest() method
        $lastMessage = $conv->messages->first();

        $otherUsername = $conv->otherUserInConversation;

        //TODO::bad code
        $preview = new ConversationPreview($lastMessage->sender->username, $otherUsername, $lastMessage->content);

        return $preview;
    }
}