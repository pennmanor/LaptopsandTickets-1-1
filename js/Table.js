function Table(col, header){
	this.columns = cloneArray(col);
	this.names = cloneArray(col);

	if(header != undefined){
		for(var i = 0; header.length > i; i++){
			this.names[i] = header[i];
		}
	}

	this.setColumns = function(col){
		this.columns = cloneArray(col);
	}
	
	this.setHeaders = function(header){
		this.names = cloneArray(this.columns);
		for(var i = 0; header.length > i; i++){
			this.names[i] = header[i];
		}
	}

	this.buildTable = function(rows){
		var table = createElement("table");
		var head = createElement("thead");
		var headRow = createElement("tr");
		var body = createElement("tbody");
		
		for(row in this.names){
			insertElementAt(createElement("td", null, this.names[row]), headRow);
		}
		for(row in rows){
			var bodyRow = createElement("tr");
			var workingRow = rows[row];
			for(col in this.columns){
				var data = workingRow[this.columns[col]];
				var cell = createElement("td");
				cell.innerHTML = data;
				insertElementAt(cell, bodyRow);
			}
			insertElementAt(bodyRow, body);
		}
		insertElementAt(headRow, head);
		insertElementAt(head, table);
		insertElementAt(body, table);
		return table;
	}
	 return this;
}