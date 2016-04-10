			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.currencies}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: cursinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nocurrencies}
							</div>
						</div>
<!-- END: cursinfo -->
<!-- BEGIN: curstable -->
						<table>

							<thead>
								<tr>
									<th>{LANG.currencysharp}</th>
								   <th>{LANG.currencyname}</th>
								   <th>{LANG.currencysymbol}</th>
								   <th>{LANG.currencyfullname}</th>
								   <th>{LANG.currencyrate} (EUR/currency)</th>
									<th></th>
								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addcurr">{LANG.addnewcurrency}</a>&nbsp;<a class="button" href="?action=updatecurrs">{LANG.currencyupdate}</a>
										</div>
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
<!-- BEGIN: currow -->
								<tr>
									<td>{CURR.id}</td>
									<td>{CURR.name}</td>
									<td>{CURR.symbol}</td>
									<td>{CURR.desc}</td>
									<td>{CURR.rate}</td>
									<td>
										<a href="?object=editcurr&curid={CURR.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
										<a href="?action=delcurr&id={CURR.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: currow -->
							</tbody>
						</table>
<!-- END: curstable -->

					</div>

				</div>

			</div>
