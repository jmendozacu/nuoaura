<?php

    class Propoza_Propoza_Helper_Data extends Mage_Core_Helper_Abstract
    {
        private $_domain = 'propoza.com';

        /**
         * Get url to sign-up page for Propoza
         * @return string
         */
        public function getSignUpPropozaUrl()
        {
            return sprintf('%s%s/accounts/create?client=magento', $this->getProtocol(), $this->getPropozaDomain());
        }

        /**
         * Get protocol used for propoza
         * @return string
         */
        public function getProtocol()
        {
            return 'https://';
        }

        /**
         * Get base domain for Propoza
         * @return string
         */
        public function getPropozaDomain()
        {
            return $this->_domain;
        }


        /**
         * Get url to dashboard constructed with either given parameter or else the stored sub-domain
         *
         * @param null $subDomain
         *
         * @return string
         */
        public function getDashboardPropozaUrl($subDomain = null)
        {
            if ($subDomain ===
                null
            )
            {
                $subDomain = Mage::getStoreConfig('propoza/general/web_address');
            }

            return sprintf('%s%s.%s', $this->getProtocol(), $subDomain, $this->getPropozaDomain());
        }


        public  function getDashboardPropozaLoginUrl($subDomain = null, $token = null)
        {
            if (isset($token)) {
                $token = sprintf('?_token=%s', $token);
            }
            return sprintf('%s/login%s', $this->getDashboardPropozaUrl($subDomain), $token);
        }

        public function getDashboardPropozaTokenUrl($subDomain = null)
        {
            return sprintf('%s/api/MerchantsApi/token.json', $this->getDashboardPropozaUrl($subDomain));
        }

        /**
         * Get url to Propoza from where to get the Quote request form
         * @return string
         */
        public function  getQuoteRequestFormUrl()
        {
            return sprintf('%s/api/MagentoQuotes/quoteRequestForm', $this->getDashboardPropozaUrl());
        }

        /**
         * Get url to test a connection to Propoza
         *
         * @param null $subDomain
         *
         * @return string
         */
        public function getConnectionTestUrl($subDomain = null)
        {
            return sprintf('%s/api/MagentoQuotes/testConnection.json', Mage::helper('propoza')->getDashboardPropozaUrl($subDomain));
        }

        /**
         * Check if request is authorized
         * @return bool
         */
        public function isRequestAuthorized()
        {
            $referrer = null;
            if (isset($_SERVER['HTTP_REFERER']))
            {
                $referrer = $_SERVER['HTTP_REFERER'];
            }
            elseif (isset($_SERVER['HTTP_ORIGIN']))
            {
                $referrer = $_SERVER['HTTP_ORIGIN'];
            }

            $referrer_parts = parse_url($referrer);
            $referrer_host = $referrer_parts['host'];

            $dashboard_parts = parse_url($this->getDashboardPropozaUrl());
            $dashboard_host = $dashboard_parts['host'];

            return $referrer_host ==
            $dashboard_host;
        }

        /**
         * Check for validity api key.
         * Should be alphanumeric, 226 characters long and end with ==
         *
         * @param $string
         * @return int
         */
        public function isValidApiKey($string)
        {
            return preg_match('/^[A-Za-z0-9+\/]{226}==$/', $string);
        }

        /**
         * Check if configuration values are set to make a request to Propoza
         *
         * @return bool
         */
        public function isConfigured()
        {
            $apiKey = $this->getApiKey();
            $subDomain = $this->getSubDomain();

            return (isset($apiKey) &&  isset($subDomain) && trim($apiKey) !== '' && trim($subDomain)!== '');
        }

        /**
         * Get api_key from store config
         * @return mixed
         */
        public function getApiKey()
        {
            return Mage::getStoreConfig('propoza/general/api_key');
        }

        /**
         * Get sub_domain from store config
         * @return mixed
         */
        public function getSubDomain()
        {
            return Mage::getStoreConfig('propoza/general/web_address');
        }
    }