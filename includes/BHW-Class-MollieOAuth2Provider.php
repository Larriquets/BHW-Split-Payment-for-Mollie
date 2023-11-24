<?php

/**
 * This class, MollieOAuth2Provider, is responsible for handling OAuth2 authentication with Mollie.
 * It manages the authorization process, retrieves access tokens, and saves Mollie organization and onboarding data for vendors.
 */

namespace Mollie\OAuth2\Client\Provider;

class MollieOAuth2Provider {

    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $provider;

    const MOLLIE_ONBOARDING_COMPLETED = 'mollie_onboarding_completed';

    public function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->provider = $this->createProvider();
    }


    private function createProvider() {
        $provider =  new \Mollie\OAuth2\Client\Provider\Mollie([
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri'  => $this->redirectUri,
        ]);
        $logger = wc_get_logger();
        $context = array('source' => 'bhw-mollie_oauth2');
        
        // If we don't have an authorization code then get one
        if (!isset($_GET['code']))
        {
            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl([
                // Optional, only use this if you want to ask for scopes the user previously denied.
                'approval_prompt' => 'force', 
                
                // Optional, a list of scopes. Defaults to only 'organizations.read'.
                'scope' => [
                    \Mollie\OAuth2\Client\Provider\Mollie::SCOPE_ORGANIZATIONS_READ, 
                    \Mollie\OAuth2\Client\Provider\Mollie::SCOPE_ORGANIZATIONS_WRITE, 
                    \Mollie\OAuth2\Client\Provider\Mollie::SCOPE_ONBOARDING_READ,
                    \Mollie\OAuth2\Client\Provider\Mollie::SCOPE_ONBOARDING_WRITE,        
                ], 
            ]);
            $logger->debug( 'BHW - authorizationUrl'.print_r($authorizationUrl,true), $context);
            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();
            $logger->debug( 'BHW - oauth2state'.print_r($oauth2state,true), $context);

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;
        }
        // If we don't have an authorization code then get one
        if (isset($_GET['code']))
        {    
            try
            {
               // echo 'Try to get an access token using the authorization code grant<br>';
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);        
                $logger->debug( 'BHW - accessToken'.print_r($accessToken,true), $context);
                // Using the access token, we may look up details about the resource owner.        
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setAccessToken($accessToken->getToken());

                $onboarding = $mollie->onboarding->get();
                $logger->debug( 'BHW - onboarding'.print_r($onboarding,true), $context);
      
                $organization = $mollie->organizations->current();
                $logger->debug( 'BHW - organization'.print_r($organization,true), $context);
      
                $this->mollie_save_organization($organization);
                $this->mollie_save_onboarding_status($onboarding);

                // Redirect the Vendor Dashabord
                $vendorDashboardUrl = home_url('/dashboard-vendor/settings/payment/');
                header('Location: ' . $vendorDashboardUrl);
                exit;
            }
            catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e)
            {
                // Failed to get the access token or user details.
                exit($e->getMessage());
            }
        } 


    }
    /**
     * Save Mollie Organization Details
     */
    function mollie_save_organization($organization) {

        $user_id = get_current_user_id();
        if( $organization ) {
            update_user_meta( $user_id, 'mollie_organization_id', $organization->id);
            update_user_meta( $user_id, 'mollie_organization_email', $organization->email);
        }
    }

    /**
     * Save Mollie Onboarding status
     */
    function mollie_save_onboarding_status($onboarding) {

        $user_id = get_current_user_id();
        if( $onboarding ) {
            update_user_meta( $user_id, 'mollie_receive_payments', $onboarding->canReceivePayments);
            update_user_meta( $user_id, 'mollie_onboarding_status', $onboarding->status);  
            $this->save(MOLLIE_ONBOARDING_COMPLETED,  $user_id );
        }

    }

    /**
     * Save onboarding status
     * @return bool true if could save
     */
    public function save($status , $user_id )
    {
        $arrStatus = $this->getAll( $user_id);
        if (is_array($arrStatus)) {
            $arrStatus[] = $status;
        } else {
            $arrStatus = [$status];
        }

        update_field('processing_and_delivery_completed', json_encode($arrStatus), 'user_' . $user_id);

        return true;
    }

    /**
     * Return all onboarding status
     * @return array(string)
     */
    public function getAll( $user_id)
    {
        $status = get_field('onboarding_status', 'user_' . $user_id);
        $arrStatus = json_decode($status, true);
        return $arrStatus;
    }


    public function getProvider() {
        return $this->provider;
    }
}
 