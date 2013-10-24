Ext.define('ecopyahuMovil.view.Denuncias.Camara', {
    extend: 'Ext.Component',
    xtype: 'capturarImagen',
    config: {
        width: 140,
        height: 100,
        cls: 'picture-capture',
        html: [
            '<div class="icon"><i class="icon-camera"></i> Realizar una foto</div>',
            '<img class="image-tns" />',
            '<!--<input type="file" capture="camera" accept="image/*" />' //Step 1-->
        ].join(''),
        listeners: {
            tap: {
                element: 'element',
                fn: function(){
                    // Dispara un evento que escucha el controller Main
                    this.fireEvent('sacar_foto');
                }
            }
        }
    }
});