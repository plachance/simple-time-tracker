<!DOCTYPE html>
<html lang="<%= $this->Application->Globalization->Culture %>" class="no-js">
	<com:THead
		ShortcutIcon="<%/ favicon.ico %>"
		Title="<%[Simple Time Tracker]%>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="<%/ components/jquery/jquery.min.js %>"></script>
		<script>$.noConflict();</script>
		<script src="<%/ components/bootstrap/js/bootstrap.min.js %>"></script>
		<script src="<%/ static/js/global.js %>"></script>
		<link href="<%/ components/bootstrap/css/bootstrap.min.css %>" rel="stylesheet" />
		<link href="<%/ static/css/global.css %>" rel="stylesheet" type="text/css" />
		<script src="<%/ static/js/modernizr/modernizr.2.7.1.min.js %>"></script>
		<script>
			Modernizr.load({
				test: Modernizr.placeholder && Modernizr.week && Modernizr.datetime,
				nope: '<%/ static/js/js-webshim/minified/polyfiller.js %>',
				complete: function() {
					webshims.polyfill("forms forms-ext");
				}
			});
		</script>
	</com:THead>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-10"><h1><%= $this->getPage()->getTitle() %></h1></div>
				<div class="col-sm-2 text-right">
					<com:TConditional Condition="$this->User->getIsGuest()">
						<prop:TrueTemplate>
							<a href="<%= $this->Service->constructUrl('login') %>" class="btn btn-sm"><%[Sign in]%> <span class="glyphicon glyphicon-log-in"></span></a>
						</prop:TrueTemplate>
						<prop:FalseTemplate>
							<%= $this->User->getName() %><a href="<%= $this->Service->constructUrl('logout') %>" title="<%[Sign out]%>" class="btn btn-sm"><span class="glyphicon glyphicon-log-out"></span></a>
						</prop:FalseTemplate>
					</com:TConditional>
				</div>
			</div>
			<com:XMenu ID="LstMenu" CssClass="append-bottom">
				<com:XListItem Value="" Text="<%[Current task]%>" />
				<com:TListItem Value="task.history" Text="<%[History]%>" />
				<com:TListItem Value="task.timesheet" Text="<%[Timesheet]%>" />
			</com:XMenu>
			<div class="row">
				<div class="col-sm-12">
					<com:TPanel ID="PnlMessages" />
					<com:TForm Attributes.role="form">
						<com:TContentPlaceHolder ID="Content" />
					</com:TForm>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 text-center small">&copy; 2014 Patrice Lachance</div>
			</div>
		</div>
	</body>
</html>