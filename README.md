#CSIG-SW-Lab-Ticker
===================
-[Configuring the system using the provided config.php file](#config-options)

-[Changing the display interface and including other datafields](#managing-the-display-format)


This project is based around the [ticker.php](ticker.php) web page. This page provides a display for the work done by the php files.


## Config Options

The config.php file allows various settings within the program to be changed.


-[Ticket Status](#ticket-status)

-[Ticket Type](#ticket-type)

-[Polling Time](#polling-time)

-[Max Tickets](#max-tickets)

-[Sort Method](#sort-method)	

-[Sort Direction](#sort-direction)

-[Department Exclusions](#department-exclusions)


<div id="ticket-status">
<h3>Ticket Status</h3>
<div class="container">
<p>Ticket status to be included are provided as a pipe seperated string of values. The order in which they are listed controls the order in which they are displayed on-screen.<br>The available options to include are:
<ul>
	<li>Open</li>
	<li>In Progress</li>
	<li>Closed</li>
	<li>On Hold</li>
</ul>
<em>WARNING: The "Closed" option is very slow due to the volume of requests produced and it can cause the site to appear unresponsive.<br>Its use is not recommended when the Max Ticket variable is not set as it essentially DoS's the site with thousands of http requests.</em>
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Display tickets that are listed as In Progress or Open */
		define( "__TICKET_STATUS" , "In Progress|Open" );
	</code>
</div>
</div>
</div>
</div>

<div id="ticket-type">
<h3>Ticket Types</h3>

<div class="container">
<p>Ticket types to be included are provided as a pipe seperated string of values.<br>The available options to include are:
<ul>
	<li>Issue</li>
	<li>Task</li>
	<li>Bug</li>
	<li>Lead</li>
	<li>Feedback</li>
</ul>
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Display all ticket types */ </br>
		define( "__TICKET_TYPE" , "Issue|Task|Bug|Lead|Feedback" );
	</code>
</div>
</div>

</div>
</div>

<div id="polling-time">
<h3>Polling Time</h3>		
<div class="container">
<p>This variable dictates how often the back-end polls for new tickets or changes to ticket statuses. It is provided as a number in seconds.<br>
A change to the polling time requires the page to be refreshed for the effects to be propagated to the end user.
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Check for updates every 15 seconds */ </br>
		define( "__REFRESH_TIME" , 15);
	</code>
</div>
</div>

</div>
</div>

<div id="max-tickets">
<h3>Max Tickets</h3>
<div class="container">
<p>This variable controls how many tickets should be displayed on the screen at once. It is usually set as a positive integer. To show all tickets, a value of -1 may be set.
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Display all tickets in list */ </br>
		define( "__MAX_TICKETS" , -1);
	</code>
</div>
</div>

</div>
</div>

<div id="sort-method">
<h3>Sort Method</h3>
<div class="container">
<p>This controls the order in which the tickets are shown on screen.
It is provided as a string.	It dictates whether it is to be sorted by ID or Creation Date. <br>
Other sort methods may be added by editing the <code>sortBy()</code> method in <i>ticker.class.php</i> to accept other <code>__SORT_METHOD</code> variables and set <code>$a()</code> and <code>$b()</code> accordingly.<br> 
The available options are:
<ul>
	<li>CREATION_DATE</li>
	<li>ID</li>
</ul>
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Display tickets sorted by Creation Date, with the oldest first */ </br>
		define( "__SORT_METHOD" , "CREATION_DATE");
	</code>
</div>
</div>

</div>
</div>

<div id="sort-direction">
<h3>Sort Direction</h3>
<div class="container">
<p>This controls whether tickets are displayed in ascending or descending order.<br>
The available options are:
<ul>
	<li>ASC</li>
	<li>DESC</li>
</ul>
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Display ticket listing with the oldest first */ </br>
		define( "__SORT_DIRECTION" , "ASC");
	</code>
</div>
</div>

</div>
</div>

<div id="department-exclusions">
<h3>Department Exclusions</h3>
<div class="container">
<p>This controls which departments to exclude tickets from and is provided as pipe seperated string. <br>There are 22 departments that tickets are pulled from and each may be excluded by providing the department name in this string.
<ul style="list-style-type:decimal">
	<li>Other</li>
	<li>Hardware</li>
	<li>Operating Systems</li>
	<li>Issue</li>
	<li>Installation</li>
	<li>BIOS</li>
	<li>Silicon / Processors</li>
	<li>Re-Work</li>
	<li>Networking</li>
	<li>Lab Access Request</li>
	<li>Fault</li>
	<li>Requisition</li>
	<li>Security</li>
	<li>Shipping</li>
	<li>Traffic Generators</li>
	<li>Administration</li>
	<li>WGC</li>
	<li>Hardware [WGC]</li>
	<li>Software [WGC]</li>
	<li>Networking [WGC]</li>
	<li>Shipping [WGC]</li>
	<li>Silicon / Processors [WGC]</li>
</ul>
</p>

<div class="example">
<h4>Example</h4>
<div class="codebox">
	<code>
		/* Do not display tickets from the following departments */ </br>
		define( "__DEPARTMENT_EXCLUSIONS", "WGC|Hardware [WGC]|Software [WGC]");
	</code>
</div>
</div>

</div>
</div>

</div>

<h2 id="display">Managing the Display Format</h2>
<div class="parent-container">

<p>
The ticker program is designed to allow support for a number of different display interfaces. Each display can be added by creating a new subdirectory in the views folder.<br>
The default format can be specified as the false return value of the ternary logic operator checking <code>$_GET['template']</code> in the config file.<br>
This means that the template can also be set by appending a get parameter to the url in the format <code>ticker.php?template=old-basic</code>
</p>

<ul>
<li><a href="#creating-a-display">Create New Display Format</a></li>
<li><a href="#including-other-datafields">Include Other Datafields</a></li>
</ul>

<div id="creating-a-display">
<h3>Creating A Display</h3>
<div class="container">
<p>
A display can be added by creating a new folder in <i>views</i> which contains at least the following 5 files.
<ul style="list-style-type: none;">
<li><a href="#header">header.txt</a></li>
<li><a href="#footer">footer.txt</a></li>
<li><a href="#index">index.txt</a></li>
<li><a href="#indexsimple">indexsimple.txt</a></li>
<li><a href="#javascript">js.txt</a></li>
</ul>
</p>

<div class="example" id="header">
<h4>Header.txt</h4>
<p>
The header file must contain the required HTML code up to and including the <code>&lt;body&gt;</code> tag at a minimum. 
It is also required to have an include for the javascript file.<br> 
Click <a href="views/basic/header.txt">here</a> to see an example header.txt file used in the basic display view.
</p>
</div>

<div class="example" id="footer">
<h4>Footer.txt</h4>
<p>
The footer file must contain the required HTML code from the <code>&lt;/body&gt;</code> tag onwards at a minimum. <br>
Click <a href="views/basic/footer.txt">here</a> to see an example footer.txt file used in the basic display view.
</p>
</div>

<div class="example" id="index">
<h4>Index.txt</h4>
<p>
The index file contains the template for the viewable webpage. 
It contains markup for the inclusion of both the header.txt and the footer.txt. <br>This is done by placing <code>@&lt;file-name-without-extension&gt;@</code> at the points in the file where the code should be included. <br><br>

It also contains the template for each data row of the display. The data will be filled into the placeholders in the form: <code>[&lt;identifier&gt;]</code>.<br>
The placeholders included by default are:
<ul style="list-style-type:none">
	<li>[ticketid]</li>
	<li>[user]</li>
	<li>[subject]</li>
	<li>[status]</li>
	<li>[dept]</li>
	<li>[creation]</li>				
	<li>[type]</li>
</ul>
There is also one idenfier that is created based on status and is used to place tickets into different CSS classes for colour-coding. It is:
<ul style="list-style-type:none">
	<li>[class]</li>
</ul>
If further datafields are required, they can be added as explained <a href="#include">here</a>.
<br>Click <a href="views/basic/index.txt">here</a> to see an example index.txt file used in the basic display view.
</p>
</div>

<div class="example" id="indexsimple">
<h4>Indexsimple.txt</h4>
<p>
The purpose of this file is to return all the updated values of the tickets to the page so changes can be propagated to the viewer.<br>
For that reason, it just consists of all the attributes of each ticket in a string that is seperated by a specific mark-up. <br>
These attributes are seperated in the javascript and passed to the addRow() function or used in the updateRow() function.

<br>Click <a href="views/basic/indexsimple.txt">here</a> to see an example indexsimple.txt file used in the basic display view.
</p>
</div>
</div>
</div>

<div id="including-other-datafields">
<h3>Including Other Datafields</h3>
<div class="container">
<p>
The Kayako API offers access to many fields of a ticket query. For this reason, not all fields are included by default. To include additional fields, changes must be in many parts of the program.
<ul style="list-style-type: none;">
<li><a href="#indextxt-1">index.txt</a></li>				
<li><a href="#indexsimpletxt-1">indexsimple.txt</a></li>

<li><a href="#tickerclassphp">ticker.class.php</a></li>
<li><a href="#jstxt">js.txt</a></li>
</ul>
</p>

<div class="example" id="indextxt-1">
<h4>Index.txt</h4>
<p>
Data is filled into the locations occupied by certain placeholders in the form: <code>[&lt;identifier&gt;]</code>.<br>
To include other datafields, the html contained within #ticketentry# must be edited to allow for more data to be displayed. This is done using additional placeholders like above.<br>
The relevant CSS must also be adjusted. 
</p>
</div>

<div class="example" id="indexsimpletxt-1">
<h4>Indexsimple.txt</h4>
<p>
Indexsimple must also be adjusted so it returns more datafields to the page. <br>
Again, this is managed by placeholders like those in index.txt. 
</div>	

<div class="example" id="tickerclassphp">
<h4>ticker.class.php</h4>
<p>
The <code>callback_ticketlist()</code> of this class must be changed so that the <code>preg_replace</code> function calls now act on the new placeholders that have been added to index and indexsimple.
</p>
</div>	

<div class="example" id="jstxt">
<h4>js.txt</h4>
<p>
The javascript must be edited to take extra datafields during the string split operations on indexsimple in <code>updateTable()</code>. <br>
The <code>addRow()</code> function must also be adapted to match the new HTML/CSS of index.txt and also to include the extra parameters now being passed to it from the additional <code>updateTable()</code> splits.
</p>
</div>				
</div>
</div>

</div>
</body>
</html>