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
								{LANG.notickets}
							</div>
						</div>
<!-- END: ticketsinfo -->
<!-- BEGIN: ticketstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ticketsharp}</th>
								   <th>{LANG.submitter}</th>
								   <th>{LANG.department}</th>
								   <th>{LANG.subject}</th>
								   <th>{LANG.status}</th>
								   <th></th>
								</tr>

							</thead>
							
							<tfoot>
								<tr>
									<td colspan="6">
									
										<div class="pagination">
											<a href="#" title="First Page">&laquo; First</a><a href="#" title="Previous Page">&laquo; Previous</a>
<!-- BEGIN: page -->
											<a href="{LINK}" class="number {CURRENT}" title="{NUM}">{NUM}</a>
<!-- END: page -->
											<a href="#" title="Next Page">Next &raquo;</a><a href="#" title="Last Page">Last &raquo;</a>
										</div> <!-- End .pagination -->
										<div class="clear"></div>
									</td>
								</tr>
							</tfoot>

							<tbody>
<!-- BEGIN: ticketsrow -->
								<tr>
									<td>{TICKET.id}</td>
									<td>{USERNAME}</td>
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
