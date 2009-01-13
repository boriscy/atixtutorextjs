<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>Arboles con Ext JS</title>
<link rel="stylesheet" type="text/css" href="ext-2.2/resources/css/ext-all.css" />
<style type="text/css">
body{
    height:100%;
    font-family:georgia;
}
</style>
<script type="text/javascript" src="ext-2.2/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="ext-2.2/ext-all-debug.js"></script>
<script type="text/javascript" src="ext-2.2/source/locale/ext-lang-es.js"></script>
<!--Contenido que sera módificado-->
<script>
Ext.BLANK_IMAGE_URL = 'ext-2.2/resources/images/default/s.gif';

Ext.onReady(function() {

    // Estas dos líneas sirven para poder presentar los mensajes de error
    Ext.QuickTips.init();
    Ext.form.Field.prototype.msgTarget = 'side';

    // Crear un nodo que cargue asicronicamente (AJAX) sus hijos
    root = new Ext.tree.AsyncTreeNode({
        text: 'Raiz',
        id: '0',
        draggable: false
    });
    
    
    /**
    * Función que permite crear nodos
    */
    function crearNodo() {
        // Se busca el nodo seleccionado
        var nodo = Ext.getCmp('arbol').getSelectionModel().selNode;
        var w = new Ext.Window({
            title: 'Crear Nodo', width: 250, modal: true,
            items: [{
                xtype: 'form', id: 'formaCrearNodo', labelWidth: 40, url: 'arbol.php',
                items:[{
                    xtype: 'hidden', name: 'accion', id: 'accion', value: 'crear'
                },{
                    xtype: 'hidden', name: 'padre', id: 'padre', value: nodo.id
                },{
                    xtype: 'textfield', fieldLabel: 'Texto', name: 'nombre', id: 'texto', allowBlank: false
                }]
            }],
            buttons: [{
                text: 'Guardar', handler: function() {
                    // Obtener la forma que recien cramos
                    var forma = Ext.getCmp('formaCrearNodo');
                    // Recuperamos el nombre del nodo
                    var nombre = forma.getForm().getValues().nombre;
                    forma.getForm().submit({
                        success: function(a, resp) {
                            //resp.result.id
                            // Busca el parent Contenedor y lo cierra
                            nodo.appendChild(new Ext.tree.TreeNode({
                                id: resp.result.id,
                                text: nombre
                            }));
                            forma. findParentByType('window').hide();
                        },
                        failure: function(a, b) {
                            // Se muestra un mensaje de error
                            Ext.MessageBox.show({
                               title: 'Error',
                               msg: 'Existion un error al crear el nodo!',
                               buttons: Ext.MessageBox.OK,
                               icon: Ext.MessageBox.ERROR
                           });
                        }
                    });
                }
            }]
        });
        w.show();
    }

    /**
    * Función que permite editar el texto de un nodo
    */
    function editarNodo() {
        var nodo = Ext.getCmp('arbol').getSelectionModel().selNode;
        (new Ext.Window({
            title: 'Editar Nodo', width: 250, modal: true,
            items: [{
                xtype: 'form', id: 'formaEditarNodo', labelWidth: 40, url: 'arbol.php',
                items:[{
                    xtype: 'hidden', name: 'accion', id: 'accion', value: 'editar'
                },{
                    xtype: 'hidden', name: 'id', id: nodo.id, value: nodo.id
                },{
                    xtype: 'textfield', fieldLabel: 'Texto', name: 'nombre', id: 'texto', allowBlank: false,
                    value: nodo.attributes.text
                }]
            }],
            buttons: [{
                text: 'Guardar', handler: function() {
                    //console.log(Ext.getCmp('formaCrearNodo').getForm());
                    forma = Ext.getCmp('formaEditarNodo');
                    // Recuperamos el nombre del nodo
                    var nombre = forma.getForm().getValues().nombre;
                    forma.getForm().submit({
                        success: function() {
                            forma. findParentByType('window').hide();
                            nodo.setText(nombre);
                        },
                        failure: function() {
                            // Se muestra un mensaje de error
                            Ext.MessageBox.show({
                               title: 'Error',
                               msg: 'Existion un error al editar el nodo!',
                               buttons: Ext.MessageBox.OK,
                               icon: Ext.MessageBox.ERROR
                           });
                        }
                    });
                }
            }]
        }) ).show();
    }

    /**
    * Función que permite borrar nodos
    */    
    function borrarNodo() {
        // Inicio de llamada al servidor
        var arbol = Ext.getCmp('arbol');
        var nodo = arbol.getSelectionModel().selNode;
        Ext.Ajax.request({
            url:'arbol.php',
            method: 'POST',
            params: {accion: 'borrar', id: nodo.id},
            success: function(resp, o) {
                // Capturar excepcion
                try{
                    // Decodifica las cadenas de texto y las convierte en JSON
                    r = Ext.decode(resp.responseText);
                }catch(e) {
                    o.failure();
                }
                // Verificar respuesta exitosa
                if(!r.success) {
                    // Se muestra un mensaje de error recibido desde el servidor
                    Ext.MessageBox.show({
                       title: 'Error',
                       msg: r.error,
                       buttons: Ext.MessageBox.OK,
                       icon: Ext.MessageBox.ERROR
                   });
                }else {
                    nodo.remove();
                }
                arbol.enable(); //Habilitar Panel
                
                return false;
            },
            //Funcion de falla
            failure: function(resp, o) {
                arbol.enable();
                // Se muestra un mensaje de error
                Ext.MessageBox.show({
                   title: 'Error',
                   msg: 'Existion un error al borrar el nodo!',
                   buttons: Ext.MessageBox.OK,
                   icon: Ext.MessageBox.ERROR
               });
            }
        });
    }
    
    // Crear Arbol
    var tree = new Ext.tree.TreePanel({
        id: 'arbol',
        loader: new Ext.tree.TreeLoader({
            url:'arbol.php',
            requestMethod:'GET',
            baseParams:{accion:'mostrar'}
        }),
        width: 250,
        height: 300,
        enableDD: true, //Permite Drag and Drop
        containerScroll: true,
        renderTo: 'arbol', //Id del tag en el cual se renderiza
        root: root,
        rootVisible: false, //No queremos ver el nodo raiz
        tbar : [{
            text: "Crear", handler: crearNodo
        },{
            text: "Editar", handler: editarNodo
        },{
            text: "Borrar", handler: borrarNodo
        }]
    });
    // Se expande el nodo raíz y se selcciona su primer hijo
    root.expand(false, false, function() { 
        root.childNodes[0].select();
    });
    
    /**
    *Evento que permite realizar el movimiento de un nodo
    */
    tree.on('movenode', function(arbol, node, oldParent, newParent, position) {
    
        if(newParent.id == oldParent.id) {
            //No realizar nada no se reemparento
            return false;
        }
        
        //Inicio de llamada al servidor
        Ext.Ajax.request({
            url:'arbol.php',
            method: 'GET',
            params: {accion: 'mover', nodo: node.id, nuevoPadre: newParent.id},
            success: function(resp, o) {
              try{
                // Decodifica las cadenas de texto y las convierte en JSON
                r = Ext.decode(resp.responseText);
                // Verificar respuesta exitosa
                if(!r.success) {
                  o.failure(); //Fallo
                }
                arbol.enable(); //Habilitar Panel
              // Capturar excepcion
              }catch(e) {
                o.failure();
              }
            },
            //Funcion de falla
            failure: function(resp, o) {
              
              arbol.suspendEvents();
              oldParent.insertBefore(node, null);
              arbol.enable(); //habilitar arbol
            }
        });
    });
});
</script>
<!-- Fin -->
<body>
    <div id="arbol" ></div>
</body>
</html>


