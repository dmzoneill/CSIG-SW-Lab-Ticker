<?php

class WebHandler
{
	private $ch = null;
		
	public function http( $url , $getfields , $postfields )
	{
		$this->ch = curl_init();
		curl_setopt( $this->ch , CURLOPT_URL, $url  . "?" . $getfields ); 
		curl_setopt( $this->ch , CURLOPT_HEADER, TRUE ); 
		curl_setopt( $this->ch , CURLOPT_RETURNTRANSFER, TRUE ); 
		curl_setopt( $this->ch , CURLOPT_POST , 1 );
		curl_setopt( $this->ch , CURLOPT_POSTFIELDS , $postfields );
		$output = curl_exec( $this->ch ); 
		
		$httpCode = curl_getinfo( $this->ch , CURLINFO_HTTP_CODE); 
		curl_close( $this->ch ); 
		
		print $httpCode . "<br>";
		print $output;
	}
	
}

?>

<html>
<body>
<form action='webhandler.class.php?num1=6' method='post'>
<input type=text name=num2 value=9>
<input type=submit>
</form> 
</body>
</html>

<?php

if( isset( $_GET[ 'num1'] ) && isset( $_POST[ 'num2'] ) )
{
	$x = new WebHandler();
	$x->http( "127.0.0.1/index.php" , "num1=" .  $_GET[ 'num1'] , "num2=" . $_POST[ 'num2']  );
}