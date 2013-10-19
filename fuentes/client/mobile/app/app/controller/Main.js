Ext.define('ecopy.controller.Main', {
    extend: 'Ext.app.Controller',
   requires: [
        'ecopy.form.Denuncia'
    ],
     config: {
        views:['Main'],
        control: {
            'button[action=enviar]': {
               tap:'mostrar'
            }
        }
    },
    init:function(){
      //  alert('app2');
        Ext.Viewport.add(Ext.create('ecopy.view.Main'));
    },
    mostrar:function(){
        alert('app2');
    }

});

