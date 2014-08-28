<script language='javascript'>

function hideOnhold()
{
	$( ".status4" ).hide( "slow" );
}

</script>

<input type=button onclick='hideOnhold()' value='doit'>

</body>
</html>


var row = "<div class='row' id='";
	row += id;
	row +="' display='none' > <div class='surface'> <span>ID: ";
	row += id;
	row +="<br></span><span>Type: ";
	row += type;
	row +="<br></span> <span>User: ";
	row += user;
	row +="<br></span><span>Status: ";
	row += status;
	row +="<br></span><span>Subject: ";
	row += subject;
	row +="<br></span></div></div>"; 