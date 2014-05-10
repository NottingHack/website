<?php /* Smarty version 2.6.18-dev, created on 2014-01-18 07:56:52
         compiled from wiki:OpenStreetMap */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'counter', 'wiki:OpenStreetMap', 1, false),array('modifier', 'escape', 'wiki:OpenStreetMap', 1, false),array('modifier', 'default', 'wiki:OpenStreetMap', 1, false),array('modifier', 'validate', 'wiki:OpenStreetMap', 14, false),)), $this); ?>
<?php echo smarty_function_counter(array('name' => 'openStreetMapDivID','assign' => 'openStreetMapDivID'), $this);?>
<div id="openStreetMap<?php echo ((is_array($_tmp=$this->_tpl_vars['openStreetMapDivID'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" style="width:<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['width'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('default', true, $_tmp, '400px') : smarty_modifier_default($_tmp, '400px')); ?>
; height:<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['height'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('default', true, $_tmp, '400px') : smarty_modifier_default($_tmp, '400px')); ?>
;"></div><p style="margin-top:0;font-size:x-small;">&copy; <a href="http://www.openstreetmap.org/">OpenStreetMap</a></p><?php if ($this->_tpl_vars['openStreetMapDivID'] == 1): ?><script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script><style>
   div.olControlMousePosition {
     bottom: 0px;
     right: 0px;
     position: absolute;
     color: #000;
     background: #fff;
     font-family: sans-serif;
     font-size: small;
     font-weight: bold;
   }
</style><?php endif; ?><script type="text/javascript">
(function (){
   var lon = <?php echo ((is_array($_tmp=$this->_tpl_vars['lon'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'float') : smarty_modifier_validate($_tmp, 'float')); ?>
;
   var lat = <?php echo ((is_array($_tmp=$this->_tpl_vars['lat'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'float') : smarty_modifier_validate($_tmp, 'float')); ?>
;
   var zoom = <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['zoom'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'int') : smarty_modifier_validate($_tmp, 'int')))) ? $this->_run_mod_handler('default', true, $_tmp, 15) : smarty_modifier_default($_tmp, 15)); ?>
;
   var divID = 'openStreetMap<?php echo ((is_array($_tmp=$this->_tpl_vars['openStreetMapDivID'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
';
   var mapUnits = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['units'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')))) ? $this->_run_mod_handler('default', true, $_tmp, 'degrees') : smarty_modifier_default($_tmp, 'degrees')); ?>
';
   var epsg4326 = new OpenLayers.Projection('EPSG:4326');

   // add the openstreetmap layer
   map = new OpenLayers.Map(divID, {
      units: mapUnits,
      controls: [
         new OpenLayers.Control.Navigation(),
         new OpenLayers.Control.ArgParser(),
         new OpenLayers.Control.PanZoomBar(),
         // display layers in reverse order so that base layers appear at the bottom
         new OpenLayers.Control.LayerSwitcher({'ascending':false})
   <?php if ($this->_tpl_vars['showmouseposition'] == 'true'): ?>
         , new OpenLayers.Control.MousePosition()
   <?php endif; ?>
      ],
      displayProjection: epsg4326
   });
   var mapnik = new OpenLayers.Layer.OSM();
   map.addLayer(mapnik);

   // transform from WGS 1984 to Spherical Mercator projection
   var mpo = map.getProjectionObject();
   var centerPt = new OpenLayers.LonLat(lon, lat).transform(epsg4326, mpo);

   map.setCenter(centerPt, zoom);

   <?php if ($this->_tpl_vars['showmarker'] == 'true'): ?>
   var markers = new OpenLayers.Layer.Markers("Markers");
   map.addLayer(markers);
   markers.addMarker(new OpenLayers.Marker(centerPt));
   <?php endif; ?>

   <?php if (( isset ( $this->_tpl_vars['poifile'] ) )): ?>
   var pois = new OpenLayers.Layer.Text(
      '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['poititle'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')))) ? $this->_run_mod_handler('default', true, $_tmp, 'Points of interest') : smarty_modifier_default($_tmp, 'Points of interest')); ?>
',
      {
         location:'<?php echo ((is_array($_tmp=$this->_tpl_vars['poifile'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
',
         projection:map.displayProjection
      });
   map.addLayer(pois);
   <?php endif; ?>

   // add the vector overlays
   var vectorPath;
   var points;
   var geometry;
   var feature;
   <?php $_from = $this->_tpl_vars['path']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cur_path']):
?>
   // construct the next path
   vectorPath = new OpenLayers.Layer.Vector('<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['cur_path']['title'])) ? $this->_run_mod_handler('default', true, $_tmp, 'Route') : smarty_modifier_default($_tmp, 'Route')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')); ?>
');
   points = [];
   <?php $_from = $this->_tpl_vars['cur_path']['lat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['pathpoint'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pathpoint']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['lat']):
        $this->_foreach['pathpoint']['iteration']++;
?>
   points.push(new OpenLayers.Geometry.Point(<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['cur_path']['lon'][($this->_foreach['pathpoint']['iteration']-1)])) ? $this->_run_mod_handler('validate', true, $_tmp, 'float') : smarty_modifier_validate($_tmp, 'float')))) ? $this->_run_mod_handler('default', true, $_tmp, 0.0) : smarty_modifier_default($_tmp, 0.0)); ?>
, <?php echo ((is_array($_tmp=$this->_tpl_vars['lat'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'float') : smarty_modifier_validate($_tmp, 'float')); ?>
).transform(epsg4326, mpo));
   <?php endforeach; endif; unset($_from); ?>
   geometry = new OpenLayers.Geometry.LineString(points);
   feature = new OpenLayers.Feature.Vector(geometry, null, {
      strokeColor: '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['cur_path']['colour'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'quotes') : smarty_modifier_escape($_tmp, 'quotes')))) ? $this->_run_mod_handler('default', true, $_tmp, '#0033cc') : smarty_modifier_default($_tmp, '#0033cc')); ?>
',
      strokeOpacity: <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['cur_path']['opacity'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'float') : smarty_modifier_validate($_tmp, 'float')))) ? $this->_run_mod_handler('default', true, $_tmp, 1.0) : smarty_modifier_default($_tmp, 1.0)); ?>
,
      strokeWidth: <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['cur_path']['width'])) ? $this->_run_mod_handler('validate', true, $_tmp, 'int') : smarty_modifier_validate($_tmp, 'int')))) ? $this->_run_mod_handler('default', true, $_tmp, 4) : smarty_modifier_default($_tmp, 4)); ?>

   });
   map.addLayer(vectorPath);
   vectorPath.addFeatures(feature);
   <?php endforeach; endif; unset($_from); ?>

   // make sure mouse wheel zooming is disabled
   var navcontrols = map.getControlsByClass('OpenLayers.Control.Navigation');
   for(var i = 0; i < navcontrols.length; ++i)
      navcontrols[i].disableZoomWheel();
})();
</script><noscript><?php if (( isset ( $this->_tpl_vars['alt_image'] ) )): ?><img src="<?php echo ((is_array($_tmp=$this->_tpl_vars['alt_image'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['alt_text'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /><?php endif; ?><p>Please enable JavaScript to view the <a href="http://www.openstreetmap.org/">OpenStreetMap</a>.</p></noscript>