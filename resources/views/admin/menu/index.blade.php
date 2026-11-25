@extends('layouts.app')

@section('content')

<div id="layout-wrapper">

	@include('admin.includes.header')

	@include('admin.includes.left_sidebar')

	<style type="text/css" media="screen">
		.jstree-btn{
			margin-left: 10px;
			margin-top: -2px;
		}

		.dropdown-menu.show {
			top: 100%!important;
		}

		.dropdown-menu.show {
			display: block;
		}

		.jstree-btn .dropdown-menu{
			font-size: 12px;
		}

		.jstree-btn .dropdown-menu>li>a {
			display: block;
			padding: 3px 10px;
			clear: both;
			font-weight: 400;
			line-height: 1.42857143;
			color: #333;
			white-space: nowrap;
		}

		.jstree-btn > .btn-xs{
			padding: 2px 4px;
			font-size: 10px;
			line-height: 1;
		}
	</style>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

	<div class="main-content">
		<div class="page-content">
			<div class="container-fluid">
				<div id="content-inner-wrapper">
					<div class="row">
						<div class="col-lg-5">
							<div class="card">
								<div class="card-body">
									<div class="box-header with-border">
										<div class="eq-section">
											<input type="text" class="form-control col-md-12" id="search-menu" placeholder="Search Menu">
										</div>
									</div>
								</div>

								<div class="card-body">
									<h4 class="card-title mb-4">
										<button type="button" data-actiontype="addparent" class="btn btn-success action-btn btn-rounded waves-effect waves-light mb-2 me-2">
											Add Menu
										</button>
									</h4>

									<div id="tree-menu"></div>

								</div>
							</div>
						</div>

						<div class="col-lg-7" id="menu-contentbox">
							<div class="box box-success">
								<div class="box-header with-border"><br>
									<p>No Contents Available !!</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<script type="text/javascript">

	var menuModule = {};
	menuModule.menuList = {};

	$(document).ready(function() {

		loadMenuTree();

		$('.dropdown-menu').on('click', function(){
			console.log('test');
		});

		$("#content-inner-wrapper").on('click', ".action-btn", function(event) {
			var action = $(this).data("actiontype");
			var id = $(this).data("id");
			performAction(action, id);
		});

		$("#search-menu").on('keyup', function() {
			$('#tree-menu').jstree('search', $(this).val());
		});


		/*plugin for jstree to additional btn on node (outside of anchor)*/
		$.jstree.plugins.addCustomBtn = function(options, parent) {
			var div = document.createElement('div');
			div.className = "btn-group jstree-btn";

			this.redraw_node = function(obj, deep, callback, force_draw) {
				obj = parent.redraw_node.call(this, obj, deep, callback, force_draw);

				var menuId = obj.getElementsByTagName('a')[0].getAttribute('data-id');
				var id = obj.getAttribute('id');
				var htmlString = '';

				htmlString += '<a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
				<i class="bx bx-caret-down"></i>\
				</a>\
				<ul class="dropdown-menu">'

				htmlString += '<li><a href="javascript:void(0)" class="action-btn" data-id="' + menuId + '" data-actiontype="addchild" ><i class="bx bx-plus-medical"></i>  Add Menu</a></li>';

				htmlString += '<li><a href="javascript:void(0)" class="action-btn" data-id="' + menuId + '" data-actiontype="edit" ><i class="fas fa-pen"></i>  Edit Menu</a></li>';

				htmlString += '</ul>';

				div.innerHTML = htmlString;
				if (obj) {
					var tmp = div.cloneNode(true);
					obj.insertBefore(tmp, obj.childNodes[2]);
				}
				return obj;
			};
		};
	});

	function performAction(action, id) {
		menuModule.selectedMenu = null;

		if ('addparent' == action) {
			$.get("{{ url('/add_menu')}}", function(responseData) {
				$("#menu-contentbox").html('');
				$("#menu-contentbox").html(responseData.result);
			});

		} else if ('addchild' == action) {
			menuModule.selectedMenu = id;

			$.get("{{ url('/add_menu')}}", function(responseData) {
				$("#menu-contentbox").html('');
				$("#menu-contentbox").html(responseData.result);
			});

		} else if ('view' == action) {
			var data = [];
			data.push({name:'menuId', value:id});

			$.get("{{ url('/view_menu')}}", data, function(responseData) {
				$("#menu-contentbox").html('');
				$("#menu-contentbox").html(responseData.result);
			});

		} else if ('edit' == action) {
			menuModule.selectedMenu = id;
			var data = [];
			data.push({name:'menuId', value:id});

			$.get("{{ url('/edit_menu')}}", data, function(responseData) {
				$("#menu-contentbox").html('');
				$("#menu-contentbox").html(responseData.result);
			});

		} else {
			var htmlString = '<div class="box box-success">\
			<div class="box-header with-border">\
			<p>No Contents Available !!</p>\
			</div>\
			</div>';

			$("#menu-contentbox").html(htmlString);
		}
	}

	function loadMenuTree() {
		$.get("{{ url('/get_menu')}}", function(responseData) {
			menuModule.menuList = responseData.result;

			$.jstree.destroy('#tree-menu');
			$('#tree-menu').html(formatOuputTreeView(responseData.result));

			$('#tree-menu').jstree({
				'core': {
					'multiple': false,
					'themes': {
						'responsive': true
					}
				},
				"plugins": [
				"addCustomBtn",
				"search",
				"state",
				]
			});
		});
	}

	/*function to prepare structure for tree view (ul li)*/
	function formatOuputTreeView(data) {
		var htmlString = "";
		htmlString += '\n\t\t<ul>';

		$.each(data, function(index, el) {
			htmlString += '\n\t<li id="' + el.enc_menu_id + '"data-jstree=\'{"icon":"bx bx-building-house"}\' >\
			\n\t\t<a data-id="' + el.enc_menu_id + '" id="' + el.enc_menu_id + '" data-actiontype="view" class="action-btn">' + el.menu_name + '</a>\
			';

			if (el.child) {
				htmlString += formatOuputTreeView(el.child);
			}

			htmlString += '\n\t</li>';
		});

		htmlString += '\n\t\t</ul>';

		return htmlString;
	}

</script>

@endsection

@section('script')