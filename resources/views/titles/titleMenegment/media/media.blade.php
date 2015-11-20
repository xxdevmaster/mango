@extends('layout')

{{--@asaha('dddccvbcbcb')--}}

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="clearfix">
						<div class="pull-left">
							<h4 class="text-right">Apio Verde / Metadata</h4>							
						</div>
						<div class="pull-right">
							<h4>Invoice # <br>
								<strong>2015-04-23654789</strong>
							</h4>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							
							<div class="pull-left col-md-2">
								<img src="http://cinecliq.assets.s3.amazonaws.com/files/jqOjL2.jpg" class="image-responsive" alt="" width="100%" height="auto">
							</div>
							<div class="pull-left col-md-3">
								<!--div class="list-group no-border">
                                  <a href="#" class="list-group-item"><span class="fa fa-circle text-info pull-right"></span>Menegment</a>
                                  <a href="#" class="list-group-item"><span class="fa fa-circle text-warning pull-right"></span>Metadata</a>
                                  <a href="#" class="list-group-item"><span class="fa fa-circle text-purple pull-right"></span>Rights</a>
                                  <a href="#" class="list-group-item"><span class="fa fa-circle text-pink pull-right"></span>S</a>
                                  <a href="#" class="list-group-item"><span class="fa fa-circle text-success pull-right"></span>Family</a>
                                </div-->
								<div class="list-group no-border mail-list ">
                                  <a href="#" class="list-group-item active"><i class="fa fa-download m-r-5"></i>Inbox <b>(8)</b></a>
                                  <a href="#" class="list-group-item"><i class="fa fa-star-o m-r-5"></i>Starred</a>
                                  <a href="#" class="list-group-item"><i class="fa fa-file-text-o m-r-5"></i>Draft <b>(20)</b></a>
                                  <a href="#" class="list-group-item"><i class="fa fa-paper-plane-o m-r-5"></i>Sent Mail</a>
                                  <a href="#" class="list-group-item"><i class="fa fa-trash-o m-r-5"></i>Trash <b>(354)</b></a>
                                </div>
							</div>
						</div>
					</div>
					<div class="m-h-50"></div>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table m-t-30">
									<thead>
										<tr><th>#</th>
										<th>Item</th>
										<th>Description</th>
										<th>Quantity</th>
										<th>Unit Cost</th>
										<th>Total</th>
									</tr></thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>LCD</td>
											<td>Lorem ipsum dolor sit amet.</td>
											<td>1</td>
											<td>$380</td>
											<td>$380</td>
										</tr>
										<tr>
											<td>2</td>
											<td>Mobile</td>
											<td>Lorem ipsum dolor sit amet.</td>
											<td>5</td>
											<td>$50</td>
											<td>$250</td>
										</tr>
										<tr>
											<td>3</td>
											<td>LED</td>
											<td>Lorem ipsum dolor sit amet.</td>
											<td>2</td>
											<td>$500</td>
											<td>$1000</td>
										</tr>
										<tr>
											<td>4</td>
											<td>LCD</td>
											<td>Lorem ipsum dolor sit amet.</td>
											<td>3</td>
											<td>$300</td>
											<td>$900</td>
										</tr>
										<tr>
											<td>5</td>
											<td>Mobile</td>
											<td>Lorem ipsum dolor sit amet.</td>
											<td>5</td>
											<td>$80</td>
											<td>$400</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row" style="border-radius: 0px;">
						<div class="col-md-3 col-md-offset-9">
							<p class="text-right"><b>Sub-total:</b> 2930.00</p>
							<p class="text-right">Discout: 12.9%</p>
							<p class="text-right">VAT: 12.9%</p>
							<hr>
							<h3 class="text-right">USD 2930.00</h3>
						</div>
					</div>
					<hr>
					<div class="hidden-print">
						<div class="pull-right">
							<a href="#" class="btn btn-inverse"><i class="fa fa-print"></i></a>
							<a href="#" class="btn btn-primary">Submit</a>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>	
@stop