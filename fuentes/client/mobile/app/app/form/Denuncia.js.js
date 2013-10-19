Ext.define('ecopy.form.Denuncia', {
    extend: 'Ext.form.Panel',
    config: {
            items: [
                {
                    xtype: 'textfield',
                    name: 'name',
                    label: 'Name'
                }
            ]
    },
    // @private
    constructor: function() {
        this.callParent(arguments);
    }
});

