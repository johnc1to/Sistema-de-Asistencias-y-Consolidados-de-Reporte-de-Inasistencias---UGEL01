<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* CloudPcPartnerAgentInstallResult File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Beta\Microsoft\Graph\Model;
/**
* CloudPcPartnerAgentInstallResult class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class CloudPcPartnerAgentInstallResult extends Entity
{

    /**
    * Gets the installStatus
    *
    * @return CloudPcPartnerAgentInstallStatus|null The installStatus
    */
    public function getInstallStatus()
    {
        if (array_key_exists("installStatus", $this->_propDict)) {
            if (is_a($this->_propDict["installStatus"], "\Beta\Microsoft\Graph\Model\CloudPcPartnerAgentInstallStatus") || is_null($this->_propDict["installStatus"])) {
                return $this->_propDict["installStatus"];
            } else {
                $this->_propDict["installStatus"] = new CloudPcPartnerAgentInstallStatus($this->_propDict["installStatus"]);
                return $this->_propDict["installStatus"];
            }
        }
        return null;
    }

    /**
    * Sets the installStatus
    *
    * @param CloudPcPartnerAgentInstallStatus $val The value to assign to the installStatus
    *
    * @return CloudPcPartnerAgentInstallResult The CloudPcPartnerAgentInstallResult
    */
    public function setInstallStatus($val)
    {
        $this->_propDict["installStatus"] = $val;
         return $this;
    }
    /**
    * Gets the isThirdPartyPartner
    *
    * @return bool|null The isThirdPartyPartner
    */
    public function getIsThirdPartyPartner()
    {
        if (array_key_exists("isThirdPartyPartner", $this->_propDict)) {
            return $this->_propDict["isThirdPartyPartner"];
        } else {
            return null;
        }
    }

    /**
    * Sets the isThirdPartyPartner
    *
    * @param bool $val The value of the isThirdPartyPartner
    *
    * @return CloudPcPartnerAgentInstallResult
    */
    public function setIsThirdPartyPartner($val)
    {
        $this->_propDict["isThirdPartyPartner"] = $val;
        return $this;
    }

    /**
    * Gets the partnerAgentName
    *
    * @return CloudPcPartnerAgentName|null The partnerAgentName
    */
    public function getPartnerAgentName()
    {
        if (array_key_exists("partnerAgentName", $this->_propDict)) {
            if (is_a($this->_propDict["partnerAgentName"], "\Beta\Microsoft\Graph\Model\CloudPcPartnerAgentName") || is_null($this->_propDict["partnerAgentName"])) {
                return $this->_propDict["partnerAgentName"];
            } else {
                $this->_propDict["partnerAgentName"] = new CloudPcPartnerAgentName($this->_propDict["partnerAgentName"]);
                return $this->_propDict["partnerAgentName"];
            }
        }
        return null;
    }

    /**
    * Sets the partnerAgentName
    *
    * @param CloudPcPartnerAgentName $val The value to assign to the partnerAgentName
    *
    * @return CloudPcPartnerAgentInstallResult The CloudPcPartnerAgentInstallResult
    */
    public function setPartnerAgentName($val)
    {
        $this->_propDict["partnerAgentName"] = $val;
         return $this;
    }
    /**
    * Gets the retriable
    *
    * @return bool|null The retriable
    */
    public function getRetriable()
    {
        if (array_key_exists("retriable", $this->_propDict)) {
            return $this->_propDict["retriable"];
        } else {
            return null;
        }
    }

    /**
    * Sets the retriable
    *
    * @param bool $val The value of the retriable
    *
    * @return CloudPcPartnerAgentInstallResult
    */
    public function setRetriable($val)
    {
        $this->_propDict["retriable"] = $val;
        return $this;
    }
}
