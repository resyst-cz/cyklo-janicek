<?php
/*------------------------------------------------------------------------
# JBCatalog
# ------------------------------------------------------------------------
# BearDev development company 
# Copyright (C) 2014 JBCatalog.com. All Rights Reserved.
# @license - http://jbcatalog.com/catalog-license/site-articles/license.html GNU/GPL
# Websites: http://www.jbcatalog.com
# Technical Support:  Forum - http://jbcatalog.com/forum/
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

class AdfMultiSelectPlugin {
    
    private function _getID(){
         $db = JFactory::getDbo();
         $db->setQuery("SELECT id FROM #__jbcatalog_plugins WHERE name = 'adfmultiselect'");
         return $db->loadResult();
    }
    //when this type selected
    public function getAdfViewJSSelected(){
        $js['multiselect'] = 'if(obj.value == '.$this->_getID().'){
                        jQuery("#seltable").show();
                    }';  
        return $js;     
    }
    public function getAdfViewJSSelectedPre(){
        $js['select'] = 'jQuery("#seltable").hide();';  
        return $js;     
    }
    function __call($method, $args) {
        return null;
    }
    //add javascript to additional field view
    public function getAdfViewJS(){
        $js = array();
        $js['select'] = '<script type="text/javascript">
                function Delete_tbl_row(element) {
			var del_index = element.parentNode.parentNode.sectionRowIndex;
			var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
			element.parentNode.parentNode.parentNode.deleteRow(del_index);
		}	
		function getObj(name) {
		  if (document.getElementById)  {  return document.getElementById(name);  }
		  else if (document.all)  {  return document.all[name];  }
		  else if (document.layers)  {  return document.layers[name];  }
		}
		function add_selval(){
			if(!getObj("addsel").value){
				return false;
			}
			var tbl_elem = getObj("seltable");
			var row = tbl_elem.insertRow(tbl_elem.rows.length - 2);
			var cell1 = document.createElement("td");
			var cell2 = document.createElement("td");
			var cell3 = document.createElement("td");
			var cell4 = document.createElement("td");
			
			cell1.innerHTML = \'<input type="hidden" name="adeslid[]" value="0" /><a href="javascript:void(0);" title="'.JText::_('COM_JBCATALOG_REMOVE').'" onClick="javascript:Delete_tbl_row(this);"><input type="hidden" value="0" name="selid[]" /><img src="'.JURI::base().'components/com_jbcatalog/images/publish_x.png" title="'.JText::_('COM_JBCATALOG_REMOVE').'" /></a>\';
			
			var inp = document.createElement("input");
			inp.type="text";
			inp.setAttribute("maxlength",255);
			inp.value = getObj("addsel").value;
			inp.name = "selnames[]";
			inp.setAttribute("size",50);
			cell2.appendChild(inp);
			row.appendChild(cell1);
			row.appendChild(cell2);
			row.appendChild(cell3);
			row.appendChild(cell4);
			
			getObj("addsel").value = "";
			
			ReAnalize_tbl_Rows("seltable");
		}
                ////		
                function ReAnalize_tbl_Rows( tbl_id ) {
			start_index =1;
			var tbl_elem = getObj(tbl_id);
			if (tbl_elem.rows[start_index]) {
				for (var i=start_index; i<tbl_elem.rows.length-2; i++) {
					
					
					
					if (i > 1) { 
						tbl_elem.rows[i].cells[2].innerHTML = \'<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="'.JText::_('COM_JBCATALOG_MOVEUP').'"><img src="components/com_jbcatalog/images/up.gif"  border="0" alt="'. JText::_('COM_JBCATALOG_MOVEUP').'"></a>\';
					} else { tbl_elem.rows[i].cells[2].innerHTML = ""; }
					if (i < (tbl_elem.rows.length - 3)) {
						tbl_elem.rows[i].cells[3].innerHTML = \'<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="'.JText::_('COM_JBCATALOG_MOVEDOWN').'"><img src="components/com_jbcatalog/images/down.gif"  border="0" alt="'.JText::_('COM_JBCATALOG_MOVEDOWN').'"></a>\';
					} else { tbl_elem.rows[i].cells[3].innerHTML = ""; }

				}
			}
		}
		
		

		
		function Up_tbl_row(element) { 
			if (element.parentNode.parentNode.sectionRowIndex > 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id; 
				
				var row = table.insertRow(sec_indx - 1);

				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				row.appendChild(cell3);
				row.appendChild(cell4);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				ReAnalize_tbl_Rows(tbl_id);
			}
		}

		function Down_tbl_row(element) { 
			if (element.parentNode.parentNode.sectionRowIndex < element.parentNode.parentNode.parentNode.rows.length - 1) {
				var sec_indx = element.parentNode.parentNode.sectionRowIndex;
				var table = element.parentNode.parentNode.parentNode;
				var tbl_id = table.parentNode.id;
				
				var row = table.insertRow(sec_indx + 2);

				row.appendChild(element.parentNode.parentNode.cells[0]);
				row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				//row.appendChild(element.parentNode.parentNode.cells[0]);
				
				var cell3 = document.createElement("td");
				var cell4 = document.createElement("td");
				row.appendChild(cell3);
				row.appendChild(cell4);
				element.parentNode.parentNode.parentNode.deleteRow(element.parentNode.parentNode.sectionRowIndex);
				
				ReAnalize_tbl_Rows(tbl_id);
			}

			
		}</script>';
        return $js;
    }
    
    public function getAdfViewExtra($params = array()){
        $adf_id = (int) $params['adfid'];
        $db = JFactory::getDbo();
        $db->setQuery(
                "SELECT * FROM #__jbcatalog_adf_select"
                ." WHERE field_id = ".$adf_id
                ." ORDER BY ordering"
                );
        $selvars = $db->loadObjectList();
        $html = '<div class="control-group">
            <hr />
            <div class="controls">     
                <table id="seltable">
                        <tr>
                                <th width="20">#</th>
                                <th>'.JText::_( 'NAME' ).'</th>
                        </tr>';
                        
                        for($i=0;$i<count($selvars);$i++){
                                $html .= "<tr>";
                                $html .= '<td><input type="hidden" name="adeslid[]" value="'.$selvars[$i]->id.'" /><a href="javascript:void(0);" title="'.JText::_('COM_JBCATALOG_REMOVE').'" onClick="javascript:Delete_tbl_row(this);"><img src="'.JURI::base().'components/com_jbcatalog/images/publish_x.png" title="'.JText::_('COM_JBCATALOG_REMOVE').'" /></a></td>';
                                $html .= "<td><input type='text' name='selnames[]' size='50' value='".htmlspecialchars(stripslashes($selvars[$i]->name),ENT_QUOTES)."' /></td>";
                                $html .= '<td>';				
                                        if($i > 0){
                                                $html .= '<a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="'.JText::_('COM_JBCATALOG_MOVEUP').'"><img src="components/com_jbcatalog/images/up.gif"  border="0" alt="'.JText::_('COM_JBCATALOG_MOVEUP').'"></a>';
                                        }
                                        $html .= '</td>';
                                        $html .= '<td>';
                                        if($i < count($selvars) - 1){
                                                $html .= '<a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="'.JText::_('COM_JBCATALOG_MOVEDOWN').'"><img src="components/com_jbcatalog/images/down.gif"  border="0" alt="'.JText::_('COM_JBCATALOG_MOVEDOWN').'"></a>';
                                        }
                                        $html .= '</td>';
                                $html .= "</tr>";
                        }
                        
                 $html .= '<tr>
                                <td colspan="2"><hr /></td>
                        </tr>
                        <tr>
                                <th><input type="button" style="cursor:pointer;" value="'.JText::_('COM_JBCATALOG_ADDCHOICE').'" onclick="add_selval();" /></th>
                                <th><input type="text" maxlength="255" size="50" name="addsel" value="" id="addsel" /></th>
                        </tr>
                </table>
          </div>  
          <hr />
        </div>';
                 
        $htmls['select'] = $html;  
        
        return $htmls;
    }
    
    public function saveAdfData($params = array()){
        $adfid = $params['adfid'];
        
        $mj = 0;
        $mjarr = array();
        $db = JFactory::getDbo();
        if(isset($_POST['selnames']) && count($_POST['selnames'])){
            foreach($_POST['selnames'] as $selname){
                if($_POST['adeslid'][$mj]){
                    $db->setQuery("UPDATE #__jbcatalog_adf_select SET name = '".addslashes($selname)."',"
                            ." ordering = {$mj} WHERE id = ".intval($_POST['adeslid'][$mj]));
                    $db->execute();
                    $mjarr[] = intval($_POST['adeslid'][$mj]); 
                }else{
                    $db->setQuery("INSERT INTO #__jbcatalog_adf_select(name,field_id,ordering)"
                    ." VALUES('".addslashes($selname)."',{$adfid},{$mj})");
                    $db->execute();
                    $mjarr[] = $db->insertid();
                }
                
                $mj++;
            }    
        }else{
        
            $db->setQuery("DELETE FROM #__jbcatalog_adf_select WHERE field_id={$adfid}");
            $db->execute();
        }  
        if(count($mjarr)){
            $db->setQuery("DELETE FROM #__jbcatalog_adf_select WHERE field_id={$adfid} AND id NOT IN (".implode(',',$mjarr).")");
            $db->execute();

        }
        
    }
    
    public function getItemEdit($params = array()){
        
        $adfid = $params["adf"]->id;
        $item_id = $params["itemid"];
        
        $db = JFactory::getDbo();
        $db->setQuery(
                "SELECT * FROM #__jbcatalog_adf_select"
                ." WHERE field_id = ".$adfid
                ." ORDER BY ordering"
                );
        $selarr = $db->loadObjectList();
        
        $db->setQuery(
                "SELECT adf_value FROM #__jbcatalog_adf_values"
                ." WHERE adf_id = ".$adfid
                ." AND item_id = ".intval($item_id)
                );
        $value = $db->loadColumn();

               
        if(!count($selarr)){
            return '';
        }
        return JHTML::_('select.genericlist',   $selarr, 'extraf['.$adfid.'][]', 'class="inputboxsel" multiple size="1"', 'id', 'name', $value );
                      
    }
    public function saveItemEdit($params = array()){
        $db = JFactory::getDbo();
        $adfid = $params["adfid"];
        $value = $params["value"];
        $itemid = $params["itemid"];
        if(count($value)){
            foreach($value as $val){
                $db->setQuery("INSERT INTO #__jbcatalog_adf_values(item_id,adf_id,adf_value,adf_text)"
                ." VALUES({$itemid},{$adfid},'".addslashes($val)."','')");
                $db->execute();
            }
        }
                    
    }
    public function getItemFEAdfValue($params = array()){
        
        $adf = $params["adf"];
        $id = $params["itemid"];
        $db = JFactory::getDbo();
        $db->setQuery(
            "SELECT GROUP_CONCAT(b.name) FROM #__jbcatalog_adf_values as a"
            ." JOIN #__jbcatalog_adf_select as b"
            ." ON b.id = a.adf_value"    
            ." WHERE a.adf_id = ".$adf->id
            ." AND a.item_id = ".intval($id)
        );
        $value = $db->loadResult();
        
        return $value;
                    
    }
    
    public function getAdfFilter($params = array()){
        
        $adf = $params["adf"];
        $value = $params["value"];
        $db = JFactory::getDbo();
        
        $db->setQuery(
                    "SELECT * FROM #__jbcatalog_adf_select"
                    ." WHERE field_id = ".$adf->id
                    ." ORDER BY ordering"
                    );
        $selarr = $db->loadObjectList();
        
        if(!count($selarr)){
                return '';
            }else{
                $null_arr[] = JHTML::_('select.option',  "", JText::_('COM_JBCATALOG_SELECT'), 'id', 'name' );

                $selarr = array_merge($null_arr,$selarr);

            }
        return JHTML::_('select.genericlist',   $selarr, 'adf_filter['.$adf->id.']', 'class="inputboxsel" size="1"', 'id', 'name', $value );
           
    }
    
    public function getAdfFilterSQL($params = array()){
        
        $tbl_pref = $params["adfid"];
        $value = $params["value"];
        $key = $tbl_pref;
        
        $filter['tbl'] = " LEFT JOIN #__jbcatalog_adf_values as v{$tbl_pref} ON a.id=v{$tbl_pref}.item_id"
        ." JOIN #__jbcatalog_adf as e{$tbl_pref} ON e{$tbl_pref}.id=v{$tbl_pref}.adf_id AND e{$tbl_pref}.published='1'";

        $filter['sql'] = " AND (v{$tbl_pref}.adf_value = '{$value}' AND v{$tbl_pref}.adf_id = {$key})";
        
        return $filter;
    }
    
    public function install(){
        
    }
    
    public function uninstall(){
        
    }
    
    public function getPluginItem(){
        return null;
    }
    public function getPluginItemSave(){
        return null;
    }
    
}
