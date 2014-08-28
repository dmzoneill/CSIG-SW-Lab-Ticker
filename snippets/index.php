<?php

class Test
{
	private static $instance  = null;
	
	private function __construct( $arr = array( 4 , 6) )
	{
		
		$this->addNum( $arr[0] , $arr[1] );
	}
	
	private function addNum( $x , $y )
	{
		print $x + $y;
	}
	
	public static function getinstance(  $arr = array( 1 , 2) )
	{
		if( self::$instance == null )
		{
			self::$instance = new Test( $arr );
		}
		
		return self::$instance;
	}
}

//$ff->addNum( 5 , 7 );

$num1 = isset( $_GET[ 'num1' ] ) ? $_GET[ 'num1' ] : 0;
$num2 = isset( $_POST[ 'num2' ] ) ? $_POST[ 'num2' ] : 0;


$ff = Test::getinstance( array( $num1 , $num2 ) );