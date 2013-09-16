function Table(col, header){
	this.columns = cloneArray(col);
	this.names = cloneArray(col);
	this.tableProperties = {"table":{}, "head-row":{}, "head-data":{},"body-row":{}, "body-data":{}};
	this.columnProcesses = [];
	this.advancedColumnProcesses = [];
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
	
	this.setProperties = function(type, prop){
		setObject(this.tableProperties[type], prop);
	}

	this.addColumnProcessor = function(col, proc){
		this.columnProcesses[col] = proc; 
	}

	this.addAdvancedColumnProcessor = function(col, proc){
		this.advancedColumnProcesses[col] = proc;
	}

	this.buildTable = function(rows){
		var table = createElement("table", this.tableProperties["table"]);
		var head = createElement("thead");
		var headRow = createElement("tr", this.tableProperties["head-row"]);
		var body = createElement("tbody");

		for(row in this.names){
			insertElementAt(createElement("td", this.tableProperties["head-data"], this.names[row]), headRow);
		}
		for(row in rows){
			var bodyRow = createElement("tr", this.tableProperties["body-row"]);
			var workingRow = rows[row];
			for(col in this.columns){
				var data = workingRow[this.columns[col]];
				var cell = createElement("td", this.tableProperties["body-data"]);
				if(this.columns[col] in this.columnProcesses){
					data = this.columnProcesses[this.columns[col]](data);
				}
				if(this.columns[col] in this.advancedColumnProcesses){
					data = this.advancedColumnProcesses[this.columns[col]](workingRow);
				}
				if(typeof data == "object")
					insertElementAt(data, cell)
				else	
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
