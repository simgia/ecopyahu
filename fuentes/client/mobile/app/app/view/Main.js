Ext.define('ecopy.view.Main', {
    extend: 'Ext.Container',

    requires: [
        'Ext.SegmentedButton'
    ],

    config: {
        cls: 'card',

        items: [
            {
                xtype: 'toolbar',
                ui: 'neutral',
                docked: 'top',
                scrollable: {
                    direction: 'horizontal',
                    indicators: false
                },
                items: [
                    {
                        text: 'Enviar',
                        iconCls:'action',
                        ui: 'action',
                        action:'enviar'
                    }
                ]
            }
        ]
    },

    // @private
    constructor: function() {
        this.on({
            scope: this,
            delegate: 'button',

            tap: 'tapHandler'
        });

        this.callParent(arguments);
    }
});

