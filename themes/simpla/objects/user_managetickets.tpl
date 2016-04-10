			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.supporttickets}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: ticketsinfo -->
						<div class="notification information png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.notickets}. <a href="?object=addpkg">{LANG.newticket}</a>
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
<!-- BEGIN: ticketsrow -->
								<tr>
									<td>{TICKET.id}</td>
									<td>{DEPNAME}</td>
									<td>{TICKET.subject}</td>
									<td>{TICKET.status}</td>

									<td><a class="button" href="?object=viewticket&ticketid={TICKET.id}">{LANG.overview}</a></td>

								</tr>
<!-- END: ticketsrow -->
							</tbody>
						</table>
<!-- END: ticketstable -->

					</div>

				</div>

			</div>
