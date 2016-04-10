			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editcurrency}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="addcurr">
					<fieldset>
						<p>
							<label>{LANG.currencyname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" value =""/>
						</p>
						<p>
							<label>{LANG.currencysymbol}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="symbol" value =""/>
						</p>
						<p>
							<label>{LANG.currencyfullname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="desc" value =""/>
						</p>
						<p>
							<label>{LANG.currencyrate}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="rate" value =""/>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
