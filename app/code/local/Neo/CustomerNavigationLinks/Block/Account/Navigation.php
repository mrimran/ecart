<?php
        /**
         * @author Pradeep Sanku
         */
        class Neo_CustomerNavigationLinks_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
        {
            /**
             * Description : Unset the Link by name in the customer Navigation
             * @author Pradeep Sanku
             * @param Name of the link to be removed 
             * @return link is removed.
             */
            public function removeLinkByName($name)
            {
                unset($this->_links[$name]);
                return $this;
            }
        }
    ?>