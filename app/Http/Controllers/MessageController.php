<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use DB;
use Auth;
use App\Http\Requests;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index($recipientID = 0){

        $dialogs = array();

        $user = Auth::user();
        if($recipientID == $user->id){
            return redirect('/messages');
        }
        $userData = $user->toArray();
        $userData['imageUrl'] = (file_exists('images/users/u_'.$userData['id'].'.png') ? '/images/users/u_'.$userData['id'].'.png' : '/img/Profile/user.png');

        $dialogs1 =  DB::table('messages')
            ->select('recipient_id as user_id')
            ->where('sender_id', $user->id)
            ->groupBy('recipient_id')
            ->get();




        $dialogs2 =  DB::table('messages')
            ->select('sender_id as user_id')
            ->where('recipient_id', $user->id)
            ->groupBy('sender_id')
            ->get();

        $dialogsAll = array_merge($dialogs1, $dialogs2); //todo array_uniq по user id

        $userIDs = array();

        foreach ($dialogsAll as $dialog){
            if(!in_array($dialog->user_id, $userIDs)){
                $userIDs[] = $dialog->user_id;
            }
        }

        foreach ($userIDs as $userID){
            $recipient = User::find($userID);
            $message =  DB::table('messages')
                ->select('sender_id', 'message', 'new', 'created_at')
                ->where('sender_id', $user->id)->where('recipient_id', $recipient->id)->orWhere('sender_id', $recipient->id)->where('recipient_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $lastSender = ($message->sender_id == $user->id ? 'You:' : /*$recipient->first_name.' '.$recipient->last_name*/'');
            $imageUrl = (file_exists('images/users/u_'.$recipient->id.'.png') ? '/images/users/u_'.$recipient->id.'.png' : '/img/Profile/user.png');
            $lastMessage = str_replace('<br />', '', $message->message);
            if(strlen($lastMessage) > 25){
               $lastMessage = substr($lastMessage, 0, 25).'...';
            }

            $dialogs[] = ['userID' => $recipient->id, 'userName' => $recipient->first_name.' '.$recipient->last_name, 'image_url' => $imageUrl, 'lastSender' => $lastSender, 'lastMessage' => $lastMessage];

        }

        return view('pages.messages', ['dialogs' => $dialogs, 'recipientID' => $recipientID, 'userData' => $userData]);
    }


    public function getMessagesHistory($recipientID){
        $response = array();
        $user = Auth::user();
        $recipient = User::find($recipientID);
        $response['recipientName'] = $recipient->first_name.' '.$recipient->last_name;
        $response['image_url'] = (file_exists('images/users/u_'.$recipient->id.'.png') ? '/images/users/u_'.$recipient->id.'.png' : '/img/Profile/user.png');
        $messages =  DB::table('messages')
            ->select('sender_id', 'message', 'new', 'created_at')
            ->where('sender_id', $recipient->id)->where('recipient_id', $user->id)->orWhere('sender_id', $user->id)->where('recipient_id', $recipient->id)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($messages as $message) {
            if($message->sender_id == $user->id){
                $message->senderName = $user->first_name.' '.$user->last_name;
                $message->image_url = (file_exists('images/users/u_'.$user->id.'.png') ? '/images/users/u_'.$user->id.'.png' : '/img/Profile/user.png');
            }
            elseif($message->sender_id == $recipient->id){
                $message->senderName = $recipient->first_name.' '.$recipient->last_name;
                $message->image_url = (file_exists('images/users/u_'.$recipient->id.'.png') ? '/images/users/u_'.$recipient->id.'.png' : '/img/Profile/user.png');
            }

            $message->created_at = new Carbon($message->created_at);
            $message->created_at = $message->created_at->format("d/m/Y, H:i");

        }

        $response['messages'] = $messages;

        return response($response, 200);
    }

    public function markMessageasAsRead($recipientID)
    {
        $response = array();
        $user = Auth::user();
        DB::table('messages')
            ->where('sender_id', $recipientID)
            ->where('recipient_id', $user->id)
            ->update(['new' => false]);

        return  $user->messages()->where('new', '=', true)->count();
    }


    public function countNewMessages($recipientID = 0)
    {
        User::messages()->where('sender_id', '=', $recipientID);
    }
}
