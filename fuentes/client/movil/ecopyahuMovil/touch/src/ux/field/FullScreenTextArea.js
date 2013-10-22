// The default TextAreaField in Sencha touch 2 is difficult to use
// on a mobile device.
//
// This when tapped to edit, this displays the text full screen, and allows
// for better reading and editing.
//
// Modified from 'BetterTextArea'
// http://colinramsay.co.uk/diary/2012/05/18/a-better-textareafield-for-sencha-touch-2/
Ext.define('Ext.ux.field.FullScreenTextArea', {
    extend: 'Ext.field.TextArea',
    xtype: 'fullscreentextarea',

    config: {
        clearIcon: false,
        scrollModifier: 1.8,
        editorMargin: 0,
        editorPanel: {
            xtype: 'sheet',
            top: 0,
            left: 0,
            bottom: 0,
            right: 0,
            layout: 'fit',
            hidden: true,
            items: [{ 
                xtype: 'textareafield', 
                style: 'background: #eeeeee;', 
                clearIcon: false
            },{
                xtype: 'toolbar', 
                docked: (Ext.os.is.Phone ? 'bottom' : 'top'),
                layout: { 
                    type: 'hbox', 
                    pack: 'center' 
                },
                items: [{ 
                    xtype: 'button', 
                    text: 'Confirmar', 
                    ui: 'confirm'
                }]
           }]
        },
        listeners: {
            tap: {
                element: 'innerElement', 
                fn: 'onFormFieldTap'
            },
            focus: {
                element: 'innerElement',
                fn: function(){ 
                    this.blur(); 
                },
                scope: this
            }
        }
    },

    applyEditorPanel: function(cfg){
        if(!this.editorPanel){
            this.editorPanel = Ext.factory(cfg, Ext.Panel);
            this.editorPanel.on('painted', this.onEditorPanelPainted, this);
            this.editorPanel.element.on('drag', this.onTouchMove, this);
        }
        return this.editorPanel;
    },

    onAccept: function(){
        var savedValue = this.editorPanel.down('textareafield').getValue();
    
        this.setValue(savedValue);
        this.hidePanel();
    },
  
    hidePanel: function(){
        this.getEditorPanel().hide();
    },

    onEditorPanelPainted: function(){
        var panel = this.getEditorPanel();
    
        panel.query('button')[0].on(
            'tap', this.onAccept, this,{ 
                single: true 
            }
        );    
        // set inner height of actual textarea
        var actualField = panel.down('textareafield');
        var sheet = panel.element.down('.x-sheet-inner');
    
        actualField.setHeight(sheet.getHeight());
        actualField.setWidth(sheet.getWidth());
    },

    onTouchMove: function(e){
        var textArea = this.getEditorPanel().down('textareafield').element.down('textarea');
        
        textArea.dom.scrollTop -= (e.deltaY / this.getScrollModifier());
    },

    onFormFieldTap: function(){
        var editor = this.getEditorPanel();

        if(!this.isInitialized){
            this.isInitialized = true;
            Ext.Viewport.add(editor);
        }
        editor.down('textareafield').setValue(this.getValue());
        editor.down('textareafield').setReadOnly(this.getReadOnly());
        editor.show();
    }
});