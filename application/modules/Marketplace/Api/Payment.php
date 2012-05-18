<?php

class Marketplace_Api_Payment {//extends Core_Api_Abstract {

    var $paypalUrlLive = "https://www.paypal.com/cgi-bin/webscr";
    var $paypalUrlTest = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    var $currencyCode = "USD";//"EUR";
    var $cmd = "_xclick";
    var $sandbox = false;
    var $paypalData = array();

    public function __construct($sandbox=false) {
        $this->sandbox = $sandbox;
        if ($this->sandbox)
            $this->paypalData['url'] = $this->paypalUrlTest;
        else
            $this->paypalData['url'] = $this->paypalUrlLive;
    }

    public function form() {
        if (!isset($this->paypalData['businessEmail']))
            throw new Exception('Please specify paypal business account email ');
        if (!isset($this->paypalData['notifyUrl']))
            throw new Exception('Please specify return url for ipn ');
        if (!isset($this->paypalData['payer_email']))
            throw new Exception('Please specify payer email ');
        if (!isset($this->paypalData['amount']))
            throw new Exception('Please specify amount ');
        $this->frm = new Zend_Form();
        $this->frm->setAttrib("id", "frmPaypal");
        $this->frm->setAction($this->paypalData['url']);
        //$this->addFormField("rm", 2);
        $this->addFormField("cmd", $this->cmd);
        $this->addFormField("business", $this->paypalData['businessEmail']);
        $this->addFormField("receiver", $this->paypalData['businessEmail']);
        $this->addFormField("charset", "utf-8");
        $this->addFormField("notify_url", $this->paypalData['notifyUrl']);
        $this->addFormField("return", $this->paypalData['returnUrl']);
        $this->addFormField("payer_email", $this->paypalData['payer_email']);
        $this->addFormField("payer_id", $this->paypalData['payer_id']);
        $this->addFormField("amount", $this->paypalData['amount']);
		if(isset($this->paypalData['custom']))
			$this->addFormField("custom", $this->paypalData['custom']);
        $this->addFormField("currency_code", $this->currencyCode);
        $this->addFormField("item_number", $this->paypalData['item_number']);
        foreach ($this->arrItem as $k => $v) {
            $i = "_" . ($k + 1);
            if ($this->cmd == "_xclick")
                $i = "";
            $this->addFormField("item_name" . $i, $v['item_name']);
            if ($i !== "") {
                $this->addFormField("item_amount" . $i, $v['amount']);
                $this->addFormField("quantity" . $i, $v['quantity']);
            }
        }
        $elt = $this->frm->createElement('button', 'submitButton', array('label' => 'Buy', 'type' => 'submit'));
        $this->frm->addElement($elt);
        return $this->frm;
    }

    public function addItem($arrItem) {
        $this->arrItem[] = $arrItem;
    }

    public function addFormField($name, $value="") {
        if ($this->sandbox && 0)
            $fieldType = "text"; 
		else
            $fieldType="hidden";
        $elt = $this->frm->createElement($fieldType, $name)->setValue($value);
        if ($this->sandbox)
            $elt->setLabel($elt->getName());
        $this->frm->addElement($elt);
    }

    public function validateNotify($arrPost) {
		$postdata="";
		foreach ($arrPost as $key=>$value) {
			$postdata.=$key."=".urlencode($value)."&";
		}
		
		$postdata .= "cmd=_notify-validate";
		$curl = curl_init($this->paypalData['url']);
		curl_setopt ($curl, CURLOPT_HEADER, 0);
		curl_setopt ($curl, CURLOPT_POST, 1);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);
		$response = curl_exec ($curl);
		curl_close ($curl);

        if ($response != 'VERIFIED' && !$this->sandbox)
            throw new Exception('Invalid IPN Transaction : ');
        elseif ($arrPost["mc_gross"] == "")
            throw new Exception('Invalid IPN mcgross : not set');
        elseif ($arrPost["payment_status"] != "Completed" && !$this->sandbox)
            throw new Exception('Invalid IPN payment_status : ' . $arrPost["payment_status"]);
        elseif ($arrPost["txn_id"] == "")
            throw new Exception('Invalid IPN txn_id : not set ');
        elseif ($arrPost["txn_type"] != "web_accept")
            throw new Exception('Invalid IPN wrong txn_type ');
        else
            return true;
    }

    public function setBusinessEmail($businessEmail) {
        $this->paypalData['businessEmail'] = $businessEmail;
    }

    public function setNumber($number) {
        $this->paypalData['item_number'] = $number;
    }

    public function setReturn($return_url) {
        $this->paypalData['returnUrl'] = $return_url;
    }

    public function setAmount($amount) {
        $this->paypalData['amount'] = $amount;
    }

    public function setCustom($custom) {
        $this->paypalData['custom'] = $custom;
    }

    public function setControllerUrl($controllerUrl) {
        $this->paypalData["notifyUrl"] = $controllerUrl . "notify";
        $this->paypalData['returnUrl'] = $controllerUrl . "return";
    }

    public function setPayer($payer_email, $payer_id) {
        $this->paypalData["payer_email"] = $payer_email;
        $this->paypalData["payer_id"] = $payer_id;
    }

}

?>
