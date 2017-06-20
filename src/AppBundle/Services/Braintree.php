<?php

namespace AppBundle\Services;

use Braintree\Configuration;
use Braintree\ClientToken;
use Braintree\Subscription;
use Braintree\CreditCard;
use Braintree\Plan;
use Braintree\Customer;
use Braintree\PaymentMethod;
use AppBundle\Entity\User;

class Braintree {

	private $token = NULL;
	private $params = [];

	public function __construct(array $params) {
		$this->params = $params;
	}

	public function setToken($user) {
        Configuration::environment($this->params['env']);
        Configuration::merchantId($this->params['merchant']);
        Configuration::publicKey($this->params['public']);
        Configuration::privateKey($this->params['private']);
        $this->token = ClientToken::generate(['customerId' => $user->getDetails()->getCustomer()]);
	}

	public function getToken() {
        return $this->token;
	}

	public function planData($plan, $field) {
        return (int)explode($field.':', $plan->description)[1];
	}

	public function getActivePlan($customer) {
		if(!$customer) {
			return false;
		}
		$activeSubscription = false; 
		if($result = Customer::find($customer)) {
			if(is_array($result->paymentMethods)) {
				foreach ($result->paymentMethods as $payment) {
					if(is_array($payment->subscriptions)) {
						foreach ($payment->subscriptions as $subscription) {
							if($subscription->status == 'Active') {
								return $this->findPlan($subscription->planId);
							}
						}
					}
				}
			}
		}

		return false;

	}

	public function getActiveSubscription($customer) {
		if(!$customer) {
			return false;
		}
		$activeSubscription = false; 
		if($result = Customer::find($customer)) {
			if(is_array($result->paymentMethods)) {
				foreach ($result->paymentMethods as $payment) {
					if(is_array($payment->subscriptions)) {
						foreach ($payment->subscriptions as $subscription) {
							if($subscription->status == 'Active') {
								return $subscription;
							}
						}
					}
				}
			}
		}

		return false;

	}

	public function findPlan($id) {
		$plans = Plan::all();
		$found = FALSE;
		foreach ($plans as $plan) {
			if($plan->id == $id) {
				$found = $plan;
				break;
			}
		}
		return $found;
	}

	public function unsubscribe($user) {
  		if($customer = $user->getDetails()->getCustomer()) {
  			if($sub = $this->getActiveSubscription($customer)) {
				$result = Subscription::cancel($sub->id);
				return TRUE;
  			} else {
  				return FALSE;
  			}
  		}
	}

	public function subscribe($plan, $user, $nonce) {
//		var_dump(BCustomer::find($user->getDetails()->getCustomer())->creditCards);
/*				if($result = BCustomer::find($user->getDetails()->getCustomer())) {
					foreach ($result->paymentMethods as $payment) {
						var_dump($payment);
						if(is_array($payment->subscriptions)) {
							foreach ($payment->subscriptions as $subscription) {
								var_dump($subscription->statusHistory);
							}
						}
					}
				}

		die;
*/
		$activeSubscription = FALSE;
		// add if plan exists
		$plan = $this->findPlan($plan);
  		if($customer = $user->getDetails()->getCustomer()) {
			if($result = Customer::find($customer)) {
				if(is_array($result->paymentMethods)) {
					foreach ($result->paymentMethods as $payment) {
						if(is_array($payment->subscriptions)) {
							foreach ($payment->subscriptions as $subscription) {
								if($subscription->status == 'Active') {
									$activeSubscription = TRUE;

									if($plan) {
										$newSub = Subscription::update($subscription->id, [
											'paymentMethodNonce' => $nonce['nonce'],
											'price' => $plan->price,
											'planId' => $plan->id
										]);
									}
								}
							}
						}
					}
					if(!$activeSubscription) {

						$result = Subscription::create([
							'paymentMethodNonce' => $nonce['nonce'],
							'planId' => $plan->id
						]);
					}
				}
				return $customer;
			} else {
		    	$result = Customer::create([
		    		'lastName' => $user->getUsername(),
				    'email' => $user->getEmail(),
					'paymentMethodNonce' => $nonce['nonce']
				]);

				$success = $result->success;

		  		$userDetails = $user->getDetails();
		  		$userDetails->setCustomer($result->customer->id);

				$newSub = Subscription::update($subscription->id, [
					'paymentMethodNonce' => $nonce['nonce'],
					'price' => $plan->price,
					'planId' => $plan->id
				]);
				return $customer;
			}
  		} else {
	    	$customer = Customer::create([
	    		'lastName' => $user->getUsername(),
			    'email' => $user->getEmail(),
				'paymentMethodNonce' => $nonce['nonce']
			]);

			if($customer->success) {
				$token = $customer->customer->paymentMethods[0]->token;

				$sub = Subscription::create([
				  'paymentMethodToken' => $token,
				  'planId' => $plan->id
				]);

				return $customer->customer->id;
			} else {
				foreach($customer->errors->deepAll() as $error) {
//					echo ($error->message);die;
					return [$error->message];
				}
			}

			return FALSE;

  		}

/*  		die;

  		if($user->getDetails()->getCustomer()) {

			if($result = Customer::find($user->getDetails()->getCustomer())) {
				$success = TRUE;
				$token = $result->paymentMethods[0]->token;
			} else {
		    	$result = Customer::create([
		    		'lastName' => $user->getUsername(),
				    'email' => $user->getEmail(),
				    'paymentMethodNonce' => $nonce['nonce']
				]);

		  		$userDetails = $user->getDetails();
		  		$userDetails->setCustomer($result->customer->id);
	            $em->persist($userDetails);
				$success = $result->success;
			}

  		} else {

	    	$result = Customer::create([
	    		'lastName' => $user->getUsername(),
			    'email' => $user->getEmail(),
				'paymentMethodNonce' => $nonce['nonce']
			]);

			$success = $result->success;

	  		$userDetails = $user->getDetails();
	  		$userDetails->setCustomer($result->customer->id);
            $em->persist($userDetails);
  		}

		if ($success) {
			$token = $result->customer->paymentMethods[0]->token;

		    echo($result->customer->id).'|';
		    echo($result->customer->paymentMethods[0]->token);
		} else {
		    foreach($result->errors->deepAll() as $error) {
		        echo($error->code . ": " . $error->message . "\n");
		    }
		}		

		$result = Subscription::create([
		  'paymentMethodToken' => $token,
		  'planId' => $plan
		]);		
*/
	}
}
