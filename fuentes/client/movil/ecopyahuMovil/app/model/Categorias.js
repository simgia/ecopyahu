Ext.define('ecopyahuMovil.model.Categorias',{
    extend: 'Ext.data.Model',
    config: {
        idProperty: 'categoria_id',
        fields: [{
            name: 'categoria_id',
            type: 'int'
	},{
            name: 'categoria_nombre',
            type: 'string'
	}]
    }
});