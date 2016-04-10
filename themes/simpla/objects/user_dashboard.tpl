
			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.recentlyplacedorders}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

<!-- BEGIN: ordersinfo -->
						<div class="notification information png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noorders}
							</div>
						</div>
<!-- END: ordersinfo -->
<!-- BEGIN: orderstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.package}</th>
								   <th>{LANG.lastinvoice}</th>
								   <th>{LANG.lastinvoice}</th>
								   <th>{LANG.dateordered}</th>
								   <th></th>

								</tr>

							</thead>

							<tbody>
<!-- BEGIN: orderrow -->
								<tr>
									<td>{ORDER.id}</td>
									<td>{PKGNAME}</td>
									<td>{ORDER.lastinv}</td>
									<td>{ORDER.status}</td>
									<td>{ORDER.orderdate}</td>
									<td>
										 <a class="button" href="?object=vieworder&orderid={ORDER.id}">{LANG.overview}</a>
									</td>
								</tr>
<!-- END: orderrow -->
							</tbody>
						</table>
<!-- END: orderstable -->

					</div> <!-- End #tab1 -->
				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->

			<div class="content-box column-left">

				<div class="content-box-header">

					<h3>{LANG.lasttickets}</h3>


				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: ticketsinfo -->
						<div class="notification information png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.notickets}. <a href="?object=addticket">{LANG.createnewticket}</a>
							</div>
						</div>
<!-- END: ticketsinfo -->
<!-- BEGIN: ticketstable -->
					<table>
						<thead>
						<tr>
								   <th>{LANG.ticketsharp}</th>
								   <th>{LANG.subject}</th>
								   <th>{LANG.lastchange}</th>
								   <th>{LANG.status}</th>
								   <th></th>
						</tr>
						</thead>
						<tbody>
<!-- BEGIN: ticketsrow -->
							<tr>
								<td>{TICKET.id}</td>
								<td>{TICKET.subject}</td>
								<td>{TICKET.date}</td>
								<td>{TICKET.status}</td>
								<td><a class="button" href="?object=viewticket&ticketid={TICKET.id}">{LANG.overview}</a></td>
							</tr>
<!-- END: ticketsrow -->
						</tbody>
							
						</table>
<!-- END: ticketstable -->
					</div> <!-- End #tab3 -->

				</div> <!-- End .content-box-content -->


			</div> <!-- End .content-box -->

			<div class="content-box column-right ">

				<div class="content-box-header"> <!-- Add the class "closed" to the Content box header to have it closed by default -->

					<h3>{LANG.unpaidinvoices}</h3>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
<!-- BEGIN: invsinfo -->
						<div class="notification information png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nounpaidinvoices}.
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
								   <th></th>
						</tr>
						</thead>
						<tbody>
<!-- BEGIN: invsrow -->
							<tr>
								<td>{INV.id}</td>
								<td>{INV.orderid}</td>
								<td>{AMOUNTlf}</td>
								<td>{INV.datedue}</td>
								<td><a class="button" href="invoice.php?id={INV.id}">{LANG.overview}</a></td>
							</tr>
<!-- END: invsrow -->
						</tbody>
							
						</table>
<!-- END: invstable -->
					</div> <!-- End #tab3 -->

				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
