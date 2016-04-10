			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.listtransactions}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: transinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.notransactions}.
							</div>
						</div>
<!-- END: transinfo -->
<!-- BEGIN: transtable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.transactsharp}</th>
								   <th>{LANG.customer}</th>
								   <th>{LANG.invoice}</th>
								   <th>{LANG.paymentgateway}</th>
								   <th>{LANG.amountin}</th>
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
<!-- BEGIN: transrow -->
								<tr>
									<td>{TRANS.id}</td>
									<td>{USERNAME}</td>
									<td>{TRANS.invoiceid}</td>
									<td>{GATEWAYNAME}</td>
									<td>{TRANS.amount}</td>
									<td>
									<a href="?action=deltrans&id={TRANS.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: transrow -->
							</tbody>
						</table>
<!-- END: transtable -->

					</div>

				</div>

			</div>
