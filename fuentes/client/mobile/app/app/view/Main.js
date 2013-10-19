Ext.define('ecopy.view.Main', {
    extend: 'Ext.Container',
    requires: [
        'Ext.SegmentedButton'
    ],
    config: {
        items: [
            /*{
                xtype: 'toolbar',
                ui: 'neutral',
                docked: 'top',
                items: [
                    {
                        text: 'Enviar',
                        iconCls:'action',
                        ui: 'action',
                        action:'enviar'
                    }
                ]
            },*/Ext.create('ecopy.form.Denuncia')
        ]
    },

    // @private
    constructor: function() {
        this.callParent(arguments);
    }
});

