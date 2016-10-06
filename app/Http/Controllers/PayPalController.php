<?php

namespace App\Http\Controllers;

use App\PayPalSubscription;
use App\Sale;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Order;

class PayPalController extends Controller
{
    public function processSubscription(Request $request)
    {
        Log::info('Ответ от PayPal, txn_type = ' . $request->txn_type);

        $customData = json_decode($request->custom, true);
        $orderId = $customData['order_id'];

        $order = Sale::find($orderId);
        $buyer = $order->buyer();


        if ($this->validateSubscription($request)) {
            Log::info('Ответ от PayPal ' . $request->txn_type . ' прошел валидацию');

            $subscription = PayPalSubscription::where('subscription_id', $request->subscr_id);

            //подписка существует
            if (isset($subscription->id)) {

                // платеж по подписке
                if ($request->txn_type == 'subscr_payment') {

                    // обновляем подписку
                    $subscription->status = 'active';
                    $subscription->payment_date = $request->payment_date;
                    $subscription->save();
                }

                // отмена подписки
                if ($request->txn_type == 'subscr_cancel') {
                    $subscription->status = 'cancelled';
                    $subscription->updated_date = date('Y-m-d H:i:s');
                    $subscription->save();
                }

                // подписка истекла
                if ($request->txn_type == 'subscr_eot') {
                    $subscription->status = 'expired';
                    $subscription->updated_date = date('Y-m-d H:i:s');
                    $subscription->save();
                }

                if ($request->txn_type == 'subscr_signup') {

                }
            } // подписка не существует
            else {

                // первый платеж по подписке
                if ($request->txn_type == 'subscr_payment') {

                    // создаем подписку
                    $payPalSubscription = PayPalSubscription::create([
                        'order_id' => $order->id,
                        'user_id' => $buyer->id,
                        'subscription_id' => $request->subscr_id,
                        'payment_date' => $request->payment_date,
                        'status' => 'active'
                    ]);

                    if(isset($payPalSubscription->id)){
                        Log::info('Подписка создана');
                    }
                }

                // создание подписки. можно было бы создавать подписку здесь, но мы создаем ее при обработке первого платежа
                if ($request->txn_type == 'subscr_signup') {

                }

                // изменение подписки. Такого быть не должно т.к. подписка еще не существует
                if ($request->txn_type == 'subscr_modify') {

                }
            }
        } else {
            // подписка не прошла валидацию
            Log::info('Ответ ' . $request->txn_type . ' не прошел валидацию');
        }
    }


    /**
     *
     * Валидация
     */
    private function validateSubscription($request)
    {
        $customData = json_decode($request->custom, true);

        $orderId = $customData['order_id'];
        $order = Sale::find($orderId);

        if (!isset($order->id)) {
            //заказ не существует
            Log::error('Валидация PayPal - заказа не существует!');
            return false;
        }

        $buyer = $order->buyer();

        //валидация для отмены подписки
        if ($request->txn_type == 'subscr_cancel') {
            /*$subscriptionService = new SubscriptionService();
            $subscription = $subscriptionService->loadBySubscriptionId($myPost['subscr_id']);

            if (!$subscription->id) {
                //подписка не существует

                return false;
            }*/
        } //валидация для платежа
        elseif ($request->txn_type == 'subscr_payment') {

            //проверяем статус платежа
            if ($request->txn_type['payment_status'] != 'Completed') {
                return false;
            }
        } //проверяем возврат платежа
        /*elseif ($myPost['reason_code'] == 'refund' && $myPost['payment_status'] == 'Refunded') {
            $transactionService = new TransactionService();
            $lastTransaction = $transactionService->getLastActiveTransactionBySubscription($myPost['subscr_id']);

            //проверяем, что платеж существует
            if (!$lastTransaction) {

                return false;
            }

            //проверяем, что сумма возврата не больше суммы платежа
            if (abs($myPost['mc_gross']) > $lastTransaction['mc_gross']) {

                return false;
            }
        }*/

        return true;
    }
}
