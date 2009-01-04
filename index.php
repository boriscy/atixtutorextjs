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
<!--Contenido que sera módificado-->
<script>
Ext.BLANK_IMAGE_URL = 'ext-2.2/resources/images/default/s.gif';

Ext.onReady(function() {
    
    //Crear un nodo que cargue asicronicamente (AJAX) sus hijos
    root = new Ext.tree.AsyncTreeNode({
        text: 'Raiz',
        id:'0',
        draggable: false
    });
    
    //Crear Arbol
    var tree = new Ext.tree.TreePanel({
        id: 'treePanel',
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
        /*tbar : [{
            text: "Crear", handler: createNode}, 
            {text: "Borrar", handler: deleteNode
        }],*/
        //Definición de eventos
        listeners: {
            //Se define el nodo actual al que se haya seleccionado para poder crear
            //hijos a partir del nodo seleccionado
            click: {fn: function(node) { currentNode = node} }
            //beforeappend: {fn: function() {currentNode.expand() } }
        }
      });
    root.expand();
    
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
                //Decodes JSON
                r = Ext.decode(resp.responseText);
                //Verificar respuesta exitosa
                if(!r.success) {
                  o.failure(); //Fallo
                }
                arbol.enable(); //Habilitar Panel
              //Capturar excepcion
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


