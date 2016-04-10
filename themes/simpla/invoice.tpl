<!-- BEGIN: inverror -->
Invoice ID not specified, not found or specified in wrong format
<!-- END: inverror -->
<!-- BEGIN: autherror -->
You are not authrized to view this invoice
<!-- END: autherror -->
<!-- BEGIN: main --> 
<html>
<head>
<title>Overview Invoice #{INV.id}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="">
<meta name="description" content="">
<link href="/themes/simpla/css/invoice.css" rel="stylesheet" type="text/css">
<link href="/themes/simpla/css/invalid.css" rel="stylesheet" type="text/css">
<link href="invalid.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="742" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <th width="742" height="101" class="header" scope="col" valign="middle">
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
      <tr>
        <td>&nbsp;</td>
        <td height="33">&nbsp;</td>
      </tr>
      <tr>
        <td width="32"></td>
        <td width="50%" height="34">
        <p class="website-title">
        <strong class="website-title-2">Invoice #</strong>{INV.id}
        </p>
		  </td>
		  <td>
		  <p class="website-title">
        <strong class="website-title-2">Total Due: </strong>{AMOUNT}
        </p>
		  </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td height="18"><p class="website-subtitle">Created: {INV.datecreated} Due: {INV.datedue}</p></td>
      </tr>
    </table></th>
  </tr>
  <tr>
    <th height="123" class="textarea" scope="col" valign="top"><br>
      <table width="657" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>		
		<td width="50%">
		<form action="{PHP._SERVER.PHP_SELF}" method="get">
		  <input type="hidden" name="id" value="{INV.id}">
		<p class="subhead" >Payment Method:

									<select name="defgw" class="small-input" onchange="this.form.submit();">
<!-- BEGIN: gwlist --> 
										<option value="{GM.id}" {DEFAULT}>{GNAME}</option>
<!-- END: gwlist -->
									</select> 
									</p>
		</form>
		</td>
      </tr>
		<tr>
		<td>&nbsp;</td>
		</tr>
      <tr>
      </tr>
		<tr>
		<td>
		{FORM}
		<input class="button" type="submit" value="Submit" {DISABLED}/>
		</form>
		</td>
		</tr>
      <tr>
      </tr>
		<tr>
		<td>&nbsp;</td>
		</tr>
      <tr>

        <td width="50%"><p class="subhead">Invoice description:</p>
          <p class="bodytext">{INV.comment}
        </p>
        </td>
      </tr>
    </table>
      <br>
 
      <br></th>
  </tr>
  <tr>
    <th height="26" scope="col"><img src="/themes/simpla/images/5.jpg" width="743" height="26"></th>
  </tr>
</table>
<br>
</body>
</html>
<!-- END: main -->
