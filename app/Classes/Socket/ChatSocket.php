<?php
namespace App\Classes\Socket;

use App\Classes\Socket\Base\BaseSocket;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Exception;
use App;
use Auth;
use Config;
use Crypt;
use App\User;
use App\Message;
use Illuminate\Session\SessionManager;
use Carbon\Carbon;
use DB;

class ChatSocket extends BaseSocket
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {

        // Create a new session handler for this client
        $session = (new SessionManager(App::getInstance()))->driver();
        // Get the cookies
        $cookies = $conn->WebSocket->request->getCookies();
        // Get the laravel's one
        $laravelCookie = urldecode($cookies[Config::get('session.cookie')]);
        // get the user session id from it
        $idSession = Crypt::decrypt($laravelCookie);
        // Set the session id to the session handler
        $session->setId($idSession);
        // Bind the session handler to the client connection
        $conn->session = $session;


        $conn->session->start();

        $userID = $conn->session->get(Auth::getName());

        // and at the end. save the session state to the store
        $conn->session->save();

        //Add new client to connections
        $this->clients->attach($conn, $userID);

        //echo "New connection! ({$conn->resourceId})\n";
        //echo "User id:  ({$userID})\n";

        foreach ($this->clients as $client){
            echo $client->resourceId.' '.$this->clients[$client]."\n";
        }


    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;

        // start the session when the user send a message
        // (refreshing it to be sure that we have access to the current state of the session)
        $from->session->start();
        // do what you wants with the session
        // for example you can test if the user is auth and get his id back like this:
        $userID = $from->session->get(Auth::getName());

        
        if (isset($userID)) {
            $user = User::find($userID);
        }

        $input = json_decode($msg, true);
        $output = array();

        switch ($input['type']){
            case 'message':

                $output['senderID'] = $userID;
                $output['type'] = 'message';
                $output['senderName'] = $user->first_name.' '.$user->last_name;
                $output['text'] = nl2br(htmlspecialchars(trim($input['text'])));
                $output['datet'] =  Carbon::now()->format("d/m/Y, H:i");

                if($input['sendToAllBuyers']){
                    $job = $user->jobs()->first();
                    $sales = $job->sales()->where('status', '=', 'in_progress')->get();
                    foreach ($sales as $sale){

                        $recipient = User::findOrFail($sale->buyer_id);

                        Message::create([
                            'sender_id' => $output['senderID'],
                            'recipient_id' => $sale->buyer_id,
                            'message' => $output['text'],
                        ]);

                        $output['countNewMessages'] =  $recipient->getNewMessages();

                        $outMsg = json_encode($output);
                        foreach ($this->clients as $client){
                            if($recipient->id == $this->clients[$client] || $userID == $this->clients[$client]){
                                $client->send($outMsg);
                            }
                        }
                    }
                }
                else{
                    $recipient = User::findOrFail($input['recipientID']);

                    Message::create([
                        'sender_id' => $output['senderID'],
                        'recipient_id' => $input['recipientID'],
                        'message' => $output['text'],
                    ]);

                    $output['countNewMessages'] =  $recipient->getNewMessages();

                    $outMsg = json_encode($output);
                    foreach ($this->clients as $client){
                        if($recipient->id == $this->clients[$client] || $userID == $this->clients[$client]){
                            $client->send($outMsg);
                        }
                    }
                }
                //echo sprintf('User %s sending message "%s" to %s'."\n", $userID, $output['text'], $input['recipientID']);
                break;
            case 'typing':
                /*$output['senderID'] = $userID;
                $output['type'] = 'typing';
                $output['senderName'] = $user->first_name.' '.$user->last_name;*/
                break;
        }




        // and at the end. save the session state to the store
        $from->session->save();
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        //echo "Client ({$conn->resourceId}) has disconected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e){
        //echo "An error has occured: {$e->getMessage()}\n";

        $conn->close();
    }
}