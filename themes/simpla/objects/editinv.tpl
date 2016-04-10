			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editinvoice} #{INV.id}</h3>

					<ul class="content-box-tabs">

						<li><a href="#tab1" class="default-tab">{LANG.invoice}</a></li> <!-- href must be unique and match the id of target div -->
						<li><a href="#addtrans">{LANG.addpayment}</a></li>
					</ul>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1">
<!-- BEGIN: gwerror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.invoiceidisnotspecified}
							</div>
						</div>
<!-- END: gwerror -->
<!-- BEGIN: invoice -->
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset class="column-left">
					<input type="hidden" name="action" value="editinv">
					<input type="hidden" name="invoiceid" value="{INV.id}">
						<p>
							<label>{LANG.order}:</label>
							#<a href="?object=editorder&orderid={ORDER.id}">{ORDER.id}</a>, {ORDER.status}
						</p>
						<p>
							<label>{LANG.customer}:</label>
							<a href="?object=managecustomer&username={USER.username}">{USER.username}</a>, {USER.status}
						</p>
<!-- BEGIN: transaction -->
						<p>
							<label>{LANG.transaction}:</label>
							#<a href="?object=managecustomer&username={USER.username}">{TRANS.id}</a> at {TRANS.date}
						</p>
<!-- END: transaction -->
						<p>
							<label>{LANG.amount}:</label>
							<input class="text-input small-input" type="text" name="amount" value="{INV.amount}">
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>

					<fieldset class="colum-right">
						<p>
							<label>{LANG.status}:</label>
							<select name="status" class="small-input">
							<option value="Unpaid" {StatusUnpaid}>{LANG.statusunpaid}</option>
							<option value="Paid" {StatusPaid}>{LANG.statuspaid}</option>
							<option value="Cancelled" {StatusCancelled}>{LANG.statuscancelled}</option>
							</select>
						</p>
						<p>
							<label>{LANG.datecreated}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="orderdate" value="{INV.datecreated}" />
						</p>

						<p>
							<label>{LANG.duedate}:</label>
							<input class="text-input small-input" type="text" id="datedue" name="nextdue" value="{INV.datedue}" />
						</p>

						<p>
							<label>{LANG.datepaid}:</label>
							<input class="text-input small-input" type="text" id="datepaid" name="firstamount" value="{INV.datepaid}" />
						</p>

						<p>
							<label>{LANG.description}:</label>
							<textarea name="comment" cols="30" rows="3">{INV.comment}</textarea>
						</p>


					</fieldset>

					</form>
<!-- END: invoice -->
					</div>
					<div class="tab-content" id="addtrans">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
						<input type="hidden" name="action" value="addtrans">
						<input type="hidden" name="invid" value="{INV.invoiceid}">
						<fieldset>
							<p><label>{LANG.datecreated}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="date" value="{CURRDATE}" />
							</p>
							<p>
							<label>{LANG.transactionamount}:</label>
								<input class="text-input small-input" type="text" id="datecreated" name="amount" value="{INV.amount}" />
							</p>
							<p>
								<label>{LANG.paymentgateway}:</label>
								<select name="paygw" class="small-input">
<!-- BEGIN: paygw -->
								<option value="{GM.id}" {DEFAULT}>{GNAME}</option>
<!-- END: paygw -->
								</select>
							</p>
							<p>
								<input name="checkbox1" type="checkbox" checked> {LANG.initiateservermodule}
							</p>
							<p>
								<input class="button" type="submit" value="Submit" {DISABLED}/>
							</p>
						</fieldset>
					</form>
					</div>

				</div>

			</div>
