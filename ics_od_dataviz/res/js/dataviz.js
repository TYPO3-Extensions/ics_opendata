jQuery(function(){

	$('.ics_od_dataviz:first').each(function() {
		var ceid = $(this).attr('id');
		var filegroupid = ceid.replace('ics_oddataviz_file_', '');
		var objContainer = $(this);
		
		var select = $(this).children('select');
		var option = select.children('option:selected');
		if (option.size() && option.attr('value')) {
			startDataviz(option.attr('value'), objContainer);
		}
	});
	
	$('.ics_od_dataviz select').change(function() {
		
	});
	
});

function startDataviz(fileid, objContainer) {
	var ceid = objContainer.attr('id');
	var prog = new ProgressBar({container: objContainer});
	var dataload = (new DataLoader({
		file: fileid
	}))
	.loadData()
	.progress(prog.update)
	.done(function(loader) {
		(new DataVisualizer({container: objContainer}))
		.view(loader)
		.progress(prog.update, prog.done)
		.done(function() {
			/*$('#' + ceid + ' table').dataTable({
				"sScrollY": 300,
				"sScrollX": 600,
				// "sScrollXInner": "110%",
				"oLanguage": {
					"sLengthMenu": "Afficher _MENU_ lignes par page",
					"sZeroRecords": "Aucun résultat",
					"sInfo": "Affichage de la ligne _START_ à _END_ sur un total de _TOTAL_",
					"sInfoEmpty": "0 ligne",
					"sInfoFiltered": "(sur un total de _MAX_ enregistrements)",
					"sSearch": "Rechercher:",
					"oPaginate": {
						"sNext": "Suivant",
						"sPrevious": "Précédent"
					  }
				}
			});*/
			$('#' + ceid + ' table').wrap('<div class="over"></div>');
			$('#' + ceid + ' table a').click(function() {
				return false;
			});
		})
	});
}

(function() {

	ProgressBar = function(opt_options) {
		'use strict';
		var options = {};
		opt_options = opt_options || {};
		options.container = opt_options.container || $('body').append('<div class="ics_od_dataviz"></div>');
		
		var divs = false;
		var progressBar = false;
		var optionsStep = {text: '', count: 0, base: 50};
		
		var create = function() {			
			options.container.append('<div class="progressContainer"><div class="progressBar"></div></div><p class="progressDescription"></p>');
			var divC = options.container.children('.progressContainer');
			var divB = divC.children('.progressBar');
			var divD = options.container.children('.progressDescription');
						
			divs = {container: divC, progressbar: divB, description: divD};
		}
		
		var progress = function(purcent) {
			var width = purcent * divs.container.width()/ 100;
			divs.progressbar.width(width).html(purcent + '%');
		}
		var changeText = function(text) {
			divs.description.html(text);
		}
		this.update = function(size, step, text) {	
			if (!divs) {
				create();
			}
			if (text != optionsStep.text) {
				changeText(text);
				optionsStep.text = text;
				optionsStep.count++;
			}
			var purcent = (optionsStep.count - 1) * optionsStep.base;
			if (step) {
				purcent += parseInt((parseInt(step) * optionsStep.base) / parseInt(size));
			} else {
				purcent += optionsStep.base;
			}
			progress(purcent);
		}
		this.done = function() {
			divs.container.remove();
			divs.description.remove();
		}
	}
	
	DataVisualizer = function(opt_options) {
		'use strict';
		var options = {};
		opt_options = opt_options || {};
		options.container = opt_options.container || $('body').append('<div class="ics_od_dataviz"></div>');
		options.progressDone = function() {};
		options.done = function() {};
		
		var divs = {};
		var progressSteps = {total: 0, count: 0, text: 'Construction du tableau'};
		
		var constructTable = function(title, header, data) {
			divs.table = document.createElement('table');
			constructCaption(title);
			constructHeader(header);
			// On fait progresser la barre de progression: Nombre d'étapes: 1 => (construction tableau + entete) n => nombre de lignes / 10 
			progressSteps.total = parseInt((data.length)/10) + 1;
			options.progress(progressSteps.total, 1, progressSteps.text);
		
			timeout(function() {
				constructBody(data);
			});		
		}
		var successCallback = function() {
			options.progressDone();
			options.container.append(divs.table);
			options.done();
		}
		var constructCaption = function(title) {
			divs.caption = document.createElement('caption');
			divs.caption.innerHTML = title;
			divs.table.appendChild(divs.caption);
		}
		var constructHeader = function(header) {
			divs.thead = document.createElement('thead');
			var tr = document.createElement('tr');
			for (var text in header) {
				var th = document.createElement('th');
				var headerData = (header[text]).replace(/"/g,'&quot;');
				th.innerHTML = '<a href="#" title="' + headerData + '">' + header[text] + '</a>';
				tr.appendChild(th);
			}
			divs.thead.appendChild(tr);
			divs.table.appendChild(divs.thead);
		}
		
		var constructBody = function(data) {
			divs.tbody = document.createElement('tbody');
			divs.table.appendChild(divs.tbody);
			constructLines(data, 0);
		}
		
		var constructLines = function(data, start) {
			if (start < data.length) {
				var end = Math.min((start + 10), data.length);
				for (var index = start; index < end; index++) {
					var tr = constructLine(data[index]);
					divs.tbody.appendChild(tr);
				}
				progressSteps.count++;
				options.progress(progressSteps.total, progressSteps.count, progressSteps.text);
				timeout(function() {
					return constructLines(data, end);
				});
			} else {
				successCallback();
				return true;
			}
		}
		var constructLine = function(line) {
			var tr = document.createElement('tr');
			for (var index in line) {
				var td = document.createElement('td');
				var lineData = (line[index]).replace(/"/g,'&quot;');
				td.innerHTML = '<a title="' + lineData + '" href="#">' + line[index] + '</a>';
				tr.appendChild(td);
			}
			return tr;
		}
		var timeout = function (callback) {
			setTimeout(callback, 100);
		}
		this.view = function(loader) {
			var title = loader.getTitle();
			var header = loader.getHeader();
			var data = loader.getData();
			timeout(function() { 
				constructTable(title, header, data); 
			});
			var obj = {};
			obj.progress = function(callback, done) {
				options.progress = callback;
				options.progressDone = done;
				return this;
			};
			obj.done = function(callback) {
				options.done = callback;
				return this;
			};
			return obj;
		}
	}
	
	DataLoader = function(opt_options) {
		'use strict';
		var options = {};
		opt_options = opt_options || {};
		options.file = opt_options.file || 0;
		options.progress = function() {};
		options.done = function() {};
		
		var title = '';
		var header = false;
		var data = [];
		var loader = this;
		
		var successCallback = function(result) {
			if (result.header) {
				title = result.title;
				header = result.header;
			}
			jQuery.each(result.data, function() {
				data.push(this);
			});
			
			options.progress(result.size, result.next, 'Récupération des données');
			if (result.next == false) {
				options.done(loader);
			} else {
				retrieveData(result.next);
			}
		}
		var retrieveData = function() {
			var params = {};
			params.tx_icsoddataviz_getdata = { file: options.file };
			if (arguments.length > 0) {
				params.tx_icsoddataviz_getdata.next = arguments[0];
			}
			jQuery.ajax({
				type: "POST",
				data: params,
				url: document.getElementsByTagName('base')[0].href + '?eID=ics_od_dataviz',
				dataType: 'json',
				success: successCallback
			});
		}
		this.getTitle = function() {
			return title;
		}
		this.getHeader = function() {
			return header;
		}
		this.getData = function() {
			return data;
		}
		this.loadData = function() {
			retrieveData();
			var obj = {};
			obj.progress = function(callback) {
				options.progress = callback;
				return this;
			};
			obj.done = function(callback) {
				options.done = callback;
				return this;
			};
			return obj;
		}
	}
	
})();