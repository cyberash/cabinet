			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">
					<h3>{LANG.edituser} #{CUSTOMER.id}</h3>

					<ul class="content-box-tabs">
			
						<li><a href="#user" class="default-tab">{LANG.genuserinfo}</a></li>
						<li><a href="#profile">Profile</a></li>
						<li><a href="#orders">{LANG.orders}</a></li>
						<li><a href="#invoices">{LANG.invoices}</a></li>
						<li><a href="#tickets">{LANG.supporttickets}</a></li>
						<li><a href="#notifications">Sent notifications</a></li>
					</ul>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="user"> <!-- This is the target div. id must match the href of this div's tab -->
					<fieldset class="column-right">
					<form>
						<p>
									<label>{LANG.customersremarks}:</label>
									<textarea class="text-input textarea wysiwyg" id="textarea" name="info" cols="79" rows="15">{CUSTOMER.info}</textarea>
									<input class="button" type="submit" value="Submit" />
						</p>
					</form>
					</fieldset>
					<div style="position: relative; float: right; padding: 12px;">
					<form>
						<p><a class="button" href="?object=delcustomer&userid={CUSTOMER.id}">{LANG.remcustomer}</a></p>
						<p><a class="button" href="?object=editcustomer&customerid={CUSTOMER.id}">{LANG.modcustomer}</a></p>
						<p><a class="button" href="?object=addorder&customerid=">{LANG.createneworder}</a></p>
					</form>
					</div>
					<fieldset>
					<form>
								<p>
									<label>{LANG.username}:</label> {CUSTOMER.username}
								</p>
								<p>
									<label>{LANG.email}:</label> {CUSTOMER.email}
								</p>
								<p>
									<label>{LANG.registeredat}:</label> {CUSTOMER.opentime}
								</p>
								<p>
									<label>{LANG.lastlogin}:</label> {CUSTOMER.lastlogin}
								</p>
								<p>
								<label>Customer's stats:</label>
								{LANG.totalinvs}: {TOTALINV} <br />
								{LANG.totalorders}: {TOTALORDERS} <br />
								Tickets: {TOTALTICKETS}<br />
								Notifications: {TOTALNOTIFICATIONS}
								</p>
								</td>
								<td>
							
					</form>
					</fieldset>
					</div> 
<!-- BEGIN: customerorders -->
					<div class="tab-content" id="orders">
<!-- BEGIN: ordersinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noorders}.
							</div>
						</div>
<!-- END: ordersinfo -->
<!-- BEGIN: orderstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.package}</th>
								   <th>{LANG.status}</th>
								   <th>{LANG.dateordered}</th>
								   <th></th>

								</tr>

							</thead>
							<tbody>
<!-- BEGIN: orderrow -->
								<tr>
									<td>{ORDER.id}</td>
									<td>{ORDER.package}</td>
									<td>{ORDER.status}</td>
									<td>{ORDER.orderdate}</td>
									<td>
										 <a href="?object=editorder&orderid={ORDER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit Meta" /></a>
										 <a href="?action=delorder&orderid={ORDER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
									</td>
								</tr>
<!-- END: orderrow -->
							</tbody>
						</table>
<!-- END: orderstable -->
					</div>

<!-- END: customerorders -->





<!-- BEGIN: customerinvs -->
					<div class="tab-content" id="invoices">
<!-- BEGIN: invsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noinvoices}.
							</div>
						</div>
<!-- END: invsinfo -->
<!-- BEGIN: invstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.invoicesharp}</th>
								   <th>{LANG.order}</th>
								   <th>{LANG.totaldue}</th>
								   <th>{LANG.duedate}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>
							<tbody>
<!-- BEGIN: invrow -->
								<tr>
									<td>{INV.id}</td>
									<td>{INV.orderid}</td>
									<td>{INV.amount}</td>
									<td>{INV.datedue}</td>
									<td>{INV.status}</td>
									<td>
										 <a href="?object=editinv&invid={INV.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										 <a href="?action=delinv&invid={INV.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
								</td>
								</tr>
<!-- END: invrow -->
							</tbody>
						</table>
<!-- END: invstable -->
					</div>
<!-- END: customerinvs -->




<!-- BEGIN: customerprofile -->
					<div class="tab-content" id="orders">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<fieldset>
					<input type="hidden" name="action" value="editprofile">
						<p>
							<label>{LANG.givennames}:</label>
							<input class="text-input small-input" type="text" name="name" value="{PROFILE.name}">
							<br><small>{LANG.givennameexample}</small>
						</p>
						<p>
							<label>{LANG.surname}:</label>
							<input class="text-input small-input" type="text" name="surname" value="{PROFILE.surname}">
							<br><small>{LANG.surnameexample}</small>
						</p>
						<p>
							<label>{LANG.company}:</label>
							<input class="text-input small-input" type="text" name="company" value="{PROFILE.company}">
						</p>
						<p>
							<label>{LANG.sex}:</label>
							<select class="small-input" name="sex">
								<option value="M" {DEFAULTSM}>{LANG.sexmale}</option>
								<option value="F" {DEFAULTSF}>{LANG.female}</option>
							</select> 
						</p>
						<p>
							<label>{LANG.phonenumber}:</label>
							<input class="text-input small-input" type="text" name="phone" value="{PROFILE.phone}">
						</p>
						<p>
							<label>{LANG.country}:</label>
							<select class="small-input" name="country">
<!-- BEGIN: countrylist -->
								<option value="{ID}" {DEFAULT}>{COUNTRYNAME}</option>
<!-- END: countrylist -->
							</select>
						</p>
						<p>
							<label>{LANG.address}:</label>
							<input class="text-input small-input" type="text" name="address" value="{PROFILE.address}">
						</p>
						<p>
							<label>{LANG.city}:</label>
							<input class="text-input small-input" type="text" name="city" value="{PROFILE.city}">
						</p>
						<p>
							<label>{LANG.postcode}:</label>
							<input class="text-input small-input" type="text" name="postcode" value="{PROFILE.postcode}">
						</p>
						<p>
							<label>{LANG.icqmasnyahoo}:</label>
							<input class="text-input small-input" type="text" name="icq" value="{PROFILE.icq}">
						</p>
						<p>
							<label>{LANG.jabber}:</label>
							<input class="text-input small-input" type="text" name="jabber" value="{PROFILE.jabber}">
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>
<!-- END: customerprofile -->




<!-- BEGIN: customertickets -->
					<div class="tab-content" id="tickets">
<!-- BEGIN: ticketsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.notickets}.
							</div>
						</div>
<!-- END: ticketsinfo -->
<!-- BEGIN: ticketstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ticketsharp}</th>
								   <th>{LANG.department}</th>
								   <th>{LANG.subject}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>
							<tbody>
<!-- BEGIN: ticketrow -->
								<tr>
									<td>{TICKET.id}</td>
									<td>{DEPNAME}</td>
									<td>{TICKET.subject}</td>
									<td>{TICKET.status}</td>
									<td><a class="button" href="?object=viewticket&ticketid={TICKET.id}">{LANG.overview}</a></td>
								</tr>
<!-- END: ticketrow -->
							</tbody>
						</table>
<!-- END: ticketstable -->
					</div>
<!-- END: customertickets -->




<!-- BEGIN: notifications -->
					<div class="tab-content" id="notifications">
<!-- BEGIN: ntinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No notifications sent to this customer
							</div>
						</div>
<!-- END: ntinfo -->
<!-- BEGIN: nttable -->
						<table>

							<thead>
								<tr>
								   <th>Notify #</th>
								   <th>Subject</th>
								   <th>Module</th>
								   <th>Status</th>
								   <th></th>

								</tr>

							</thead>
							<tbody>
<!-- BEGIN: ntrow -->
								<tr>
									<td>{NT.id}</td>
									<td>{NT.subject}</td>
									<td>{NT.date}</td>
									<td>{NT.status}</td>
									<td><a class="button" href="">{LANG.overview}</a></td>
								</tr>
<!-- END: ntrow -->
							</tbody>
						</table>
<!-- END: nttable -->
					</div>
<!-- END: notifications -->


				</div> <!-- End .content-box-content -->

			</div>
