<?php 
class WebUser extends CWebUser
{

   public function logout($destroySession = true)
   {
      if ($this->allowAutoLogin && isset($this->identityCookie['domain']))
      {
         $cookies = Yii::app()->getRequest()->getCookies();

         if (null !== ($cookie = $cookies[$this->getStateKeyPrefix()]))
         {
            $originalCookie = new CHttpCookie($cookie->name, $cookie->value);
            $cookie->domain = $this->identityCookie['domain'];
            $cookies->remove($this->getStateKeyPrefix());
            $cookies->add($originalCookie->name, $originalCookie);
         }
      }
      
      parent::logout($destroySession);

   }

}
?>