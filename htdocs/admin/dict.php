<?php
/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2010-2013 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2011      Philippe Grand       <philippe.grand@atoo-net.com>
 * Copyright (C) 2011      Remy Younes          <ryounes@gmail.com>
 * Copyright (C) 2012-2015 Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2012      Christophe Battarel	<christophe.battarel@ltairis.fr>
 * Copyright (C) 2011-2015 Alexandre Spangaro	<aspangaro.dolibarr@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	    \file       htdocs/admin/dict.php
 *		\ingroup    setup
 *		\brief      Page to administer data tables
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$langs->load("errors");
$langs->load("admin");
$langs->load("companies");
$langs->load("resource");

$action=GETPOST('action','alpha')?GETPOST('action','alpha'):'view';
$confirm=GETPOST('confirm','alpha');
$id=GETPOST('id','int');
$rowid=GETPOST('rowid','alpha');

if (!$user->admin) accessforbidden();

$acts[0] = "activate";
$acts[1] = "disable";
$actl[0] = img_picto($langs->trans("Disabled"),'switch_off');
$actl[1] = img_picto($langs->trans("Activated"),'switch_on');

$listoffset=GETPOST('listoffset');
$listlimit=GETPOST('listlimit')>0?GETPOST('listlimit'):1000;
$active = 1;

$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if ($page == -1) { $page = 0 ; }
$offset = $listlimit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('admin'));

// This page is a generic page to edit dictionaries
// Put here declaration of dictionaries properties

// Sort order to show dictionary (0 is space). All other dictionaries (added by modules) will be at end of this.
$taborder=array(9,0,4,3,2,0,1,8,19,16,27,0,5,11,0,6,0,29,0,7,17,24,28,0,10,23,12,13,0,14,0,22,20,18,21,0,15,0,25,0,26);

// Name of SQL tables of dictionaries
$tabname=array();
$tabname[1] = MAIN_DB_PREFIX."c_forme_juridique";
$tabname[2] = MAIN_DB_PREFIX."c_departements";
$tabname[3] = MAIN_DB_PREFIX."c_regions";
$tabname[4] = MAIN_DB_PREFIX."c_country";
$tabname[5] = MAIN_DB_PREFIX."c_civility";
$tabname[6] = MAIN_DB_PREFIX."c_actioncomm";
$tabname[7] = MAIN_DB_PREFIX."c_chargesociales";
$tabname[8] = MAIN_DB_PREFIX."c_typent";
$tabname[9] = MAIN_DB_PREFIX."c_currencies";
$tabname[10]= MAIN_DB_PREFIX."c_tva";
$tabname[11]= MAIN_DB_PREFIX."c_type_contact";
$tabname[12]= MAIN_DB_PREFIX."c_payment_term";
$tabname[13]= MAIN_DB_PREFIX."c_paiement";
$tabname[14]= MAIN_DB_PREFIX."c_ecotaxe";
$tabname[15]= MAIN_DB_PREFIX."c_paper_format";
$tabname[16]= MAIN_DB_PREFIX."c_prospectlevel";
$tabname[17]= MAIN_DB_PREFIX."c_type_fees";
$tabname[18]= MAIN_DB_PREFIX."c_shipment_mode";
$tabname[19]= MAIN_DB_PREFIX."c_effectif";
$tabname[20]= MAIN_DB_PREFIX."c_input_method";
$tabname[21]= MAIN_DB_PREFIX."c_availability";
$tabname[22]= MAIN_DB_PREFIX."c_input_reason";
$tabname[23]= MAIN_DB_PREFIX."c_revenuestamp";
$tabname[24]= MAIN_DB_PREFIX."c_type_resource";
$tabname[25]= MAIN_DB_PREFIX."c_email_templates";
$tabname[26]= MAIN_DB_PREFIX."c_units";
$tabname[27]= MAIN_DB_PREFIX."c_stcomm";
$tabname[28]= MAIN_DB_PREFIX."c_holiday_types";
$tabname[29]= MAIN_DB_PREFIX."c_lead_status";

// Dictionary labels
$tablib=array();
$tablib[1] = "DictionaryCompanyJuridicalType";
$tablib[2] = "DictionaryCanton";
$tablib[3] = "DictionaryRegion";
$tablib[4] = "DictionaryCountry";
$tablib[5] = "DictionaryCivility";
$tablib[6] = "DictionaryActions";
$tablib[7] = "DictionarySocialContributions";
$tablib[8] = "DictionaryCompanyType";
$tablib[9] = "DictionaryCurrency";
$tablib[10]= "DictionaryVAT";
$tablib[11]= "DictionaryTypeContact";
$tablib[12]= "DictionaryPaymentConditions";
$tablib[13]= "DictionaryPaymentModes";
$tablib[14]= "DictionaryEcotaxe";
$tablib[15]= "DictionaryPaperFormat";
$tablib[16]= "DictionaryProspectLevel";
$tablib[17]= "DictionaryFees";
$tablib[18]= "DictionarySendingMethods";
$tablib[19]= "DictionaryStaff";
$tablib[20]= "DictionaryOrderMethods";
$tablib[21]= "DictionaryAvailability";
$tablib[22]= "DictionarySource";
$tablib[23]= "DictionaryRevenueStamp";
$tablib[24]= "DictionaryResourceType";
$tablib[25]= "DictionaryEMailTemplates";
$tablib[26]= "DictionaryUnits";
$tablib[27]= "DictionaryProspectStatus";
$tablib[28]= "DictionaryHolidayTypes";
$tablib[29]= "DictionaryOpportunityStatus";

// Requests to extract data
$tabsql=array();
$tabsql[1] = "SELECT f.rowid as rowid, f.code, f.libelle, c.code as country_code, c.label as country, f.active FROM ".MAIN_DB_PREFIX."c_forme_juridique as f, ".MAIN_DB_PREFIX."c_country as c WHERE f.fk_pays=c.rowid";
$tabsql[2] = "SELECT d.rowid as rowid, d.code_departement as code, d.nom as libelle, d.fk_region as region_id, r.nom as region, c.code as country_code, c.label as country, d.active FROM ".MAIN_DB_PREFIX."c_departements as d, ".MAIN_DB_PREFIX."c_regions as r, ".MAIN_DB_PREFIX."c_country as c WHERE d.fk_region=r.code_region and r.fk_pays=c.rowid and r.active=1 and c.active=1";
$tabsql[3] = "SELECT r.rowid as rowid, r.code_region as code, r.nom as libelle, r.fk_pays as country_id, c.code as country_code, c.label as country, r.active FROM ".MAIN_DB_PREFIX."c_regions as r, ".MAIN_DB_PREFIX."c_country as c WHERE r.fk_pays=c.rowid and c.active=1";
$tabsql[4] = "SELECT c.rowid as rowid, c.code, c.label, c.active, c.favorite FROM ".MAIN_DB_PREFIX."c_country AS c";
$tabsql[5] = "SELECT c.rowid as rowid, c.code as code, c.label, c.active FROM ".MAIN_DB_PREFIX."c_civility AS c";
$tabsql[6] = "SELECT a.id    as rowid, a.code as code, a.libelle AS libelle, a.type, a.active, a.module, a.color, a.position FROM ".MAIN_DB_PREFIX."c_actioncomm AS a";
$tabsql[7] = "SELECT a.id    as rowid, a.code as code, a.libelle AS libelle, a.accountancy_code as accountancy_code, a.deductible, c.code as country_code, c.label as country, a.fk_pays as country_id, a.active FROM ".MAIN_DB_PREFIX."c_chargesociales AS a, ".MAIN_DB_PREFIX."c_country as c WHERE a.fk_pays=c.rowid and c.active=1";
$tabsql[8] = "SELECT t.id    as rowid, t.code as code, t.libelle, t.fk_country as country_id, c.code as country_code, c.label as country, t.active FROM ".MAIN_DB_PREFIX."c_typent as t LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON t.fk_country=c.rowid";
$tabsql[9] = "SELECT c.code_iso as code, c.label, c.unicode, c.active FROM ".MAIN_DB_PREFIX."c_currencies AS c";
$tabsql[10]= "SELECT t.rowid, t.taux, t.localtax1_type, t.localtax1, t.localtax2_type, t.localtax2, c.label as country, c.code as country_code, t.fk_pays as country_id, t.recuperableonly, t.note, t.active, t.accountancy_code_sell, t.accountancy_code_buy FROM ".MAIN_DB_PREFIX."c_tva as t, ".MAIN_DB_PREFIX."c_country as c WHERE t.fk_pays=c.rowid";
$tabsql[11]= "SELECT t.rowid as rowid, element, source, code, libelle, active FROM ".MAIN_DB_PREFIX."c_type_contact AS t";
$tabsql[12]= "SELECT c.rowid as rowid, c.code, c.libelle, c.libelle_facture, c.nbjour, c.fdm, c.decalage, c.active, c.sortorder FROM ".MAIN_DB_PREFIX.'c_payment_term AS c';
$tabsql[13]= "SELECT c.id    as rowid, c.code, c.libelle, c.type, c.active, c.accountancy_code FROM ".MAIN_DB_PREFIX."c_paiement AS c";
$tabsql[14]= "SELECT e.rowid as rowid, e.code as code, e.libelle, e.price, e.organization, e.fk_pays as country_id, c.code as country_code, c.label as country, e.active FROM ".MAIN_DB_PREFIX."c_ecotaxe AS e, ".MAIN_DB_PREFIX."c_country as c WHERE e.fk_pays=c.rowid and c.active=1";
$tabsql[15]= "SELECT rowid   as rowid, code, label as libelle, width, height, unit, active FROM ".MAIN_DB_PREFIX."c_paper_format";
$tabsql[16]= "SELECT code, label as libelle, sortorder, active FROM ".MAIN_DB_PREFIX."c_prospectlevel";
$tabsql[17]= "SELECT id      as rowid, code, label, accountancy_code, active FROM ".MAIN_DB_PREFIX."c_type_fees";
$tabsql[18]= "SELECT rowid   as rowid, code, libelle, tracking, active FROM ".MAIN_DB_PREFIX."c_shipment_mode";
$tabsql[19]= "SELECT id      as rowid, code, libelle, active FROM ".MAIN_DB_PREFIX."c_effectif";
$tabsql[20]= "SELECT rowid   as rowid, code, libelle, active FROM ".MAIN_DB_PREFIX."c_input_method";
$tabsql[21]= "SELECT c.rowid as rowid, code, label, active FROM ".MAIN_DB_PREFIX."c_availability AS c";
$tabsql[22]= "SELECT rowid   as rowid, code, label, active FROM ".MAIN_DB_PREFIX."c_input_reason";
$tabsql[23]= "SELECT t.rowid as rowid, t.taux, c.label as country, c.code as country_code, t.fk_pays as country_id, t.note, t.active, t.accountancy_code_sell, t.accountancy_code_buy FROM ".MAIN_DB_PREFIX."c_revenuestamp as t, ".MAIN_DB_PREFIX."c_country as c WHERE t.fk_pays=c.rowid";
$tabsql[24]= "SELECT rowid   as rowid, code, label, active FROM ".MAIN_DB_PREFIX."c_type_resource";
$tabsql[25]= "SELECT rowid   as rowid, label, type_template, private, position, topic, content, active FROM ".MAIN_DB_PREFIX."c_email_templates";
$tabsql[26]= "SELECT rowid   as rowid, code, label, short_label, active FROM ".MAIN_DB_PREFIX."c_units";
$tabsql[27]= "SELECT id      as rowid, code, libelle, active FROM ".MAIN_DB_PREFIX."c_stcomm";
$tabsql[28]= "SELECT h.rowid as rowid, h.code, h.label, h.delay, h.newByMonth, h.fk_country as country_id, c.code as country_code, c.label as country, h.active FROM ".MAIN_DB_PREFIX."c_holiday_types as h LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON h.fk_country=c.rowid";
$tabsql[29]= "SELECT rowid   as rowid, code, label, percent, position, active FROM ".MAIN_DB_PREFIX."c_lead_status";

// Criteria to sort dictionaries
$tabsqlsort=array();
$tabsqlsort[1] ="country ASC, code ASC";
$tabsqlsort[2] ="country ASC, code ASC";
$tabsqlsort[3] ="country ASC, code ASC";
$tabsqlsort[4] ="code ASC";
$tabsqlsort[5] ="label ASC";
$tabsqlsort[6] ="a.type ASC, a.module ASC, a.position ASC, a.code ASC";
$tabsqlsort[7] ="country ASC, code ASC, a.libelle ASC";
$tabsqlsort[8] ="country DESC, libelle ASC";
$tabsqlsort[9] ="label ASC";
$tabsqlsort[10]="country ASC, taux ASC, recuperableonly ASC, localtax1 ASC, localtax2 ASC";
$tabsqlsort[11]="element ASC, source ASC, code ASC";
$tabsqlsort[12]="sortorder ASC, code ASC";
$tabsqlsort[13]="code ASC";
$tabsqlsort[14]="country ASC, e.organization ASC, code ASC";
$tabsqlsort[15]="rowid ASC";
$tabsqlsort[16]="sortorder ASC";
$tabsqlsort[17]="code ASC";
$tabsqlsort[18]="code ASC, libelle ASC";
$tabsqlsort[19]="id ASC";
$tabsqlsort[20]="code ASC, libelle ASC";
$tabsqlsort[21]="code ASC, label ASC";
$tabsqlsort[22]="code ASC, label ASC";
$tabsqlsort[23]="country ASC, taux ASC";
$tabsqlsort[24]="code ASC,label ASC";
$tabsqlsort[25]="label ASC";
$tabsqlsort[26]="code ASC";
$tabsqlsort[27]="code ASC";
$tabsqlsort[28]="country ASC, code ASC";
$tabsqlsort[29]="position ASC";

// Nom des champs en resultat de select pour affichage du dictionnaire
$tabfield=array();
$tabfield[1] = "code,libelle,country";
$tabfield[2] = "code,libelle,region_id,region,country";   // "code,libelle,region,country_code-country"
$tabfield[3] = "code,libelle,country_id,country";
$tabfield[4] = "code,label";
$tabfield[5] = "code,label";
$tabfield[6] = "code,libelle,type,color,position";
$tabfield[7] = "code,libelle,country,accountancy_code,deductible";
$tabfield[8] = "code,libelle,country_id,country";
$tabfield[9] = "code,label,unicode";
$tabfield[10]= "country_id,country,taux,recuperableonly,localtax1_type,localtax1,localtax2_type,localtax2,accountancy_code_sell,accountancy_code_buy,note";
$tabfield[11]= "element,source,code,libelle";
$tabfield[12]= "code,libelle,libelle_facture,nbjour,fdm,decalage,sortorder";
$tabfield[13]= "code,libelle,type,accountancy_code";
$tabfield[14]= "code,libelle,price,organization,country_id,country";
$tabfield[15]= "code,libelle,width,height,unit";
$tabfield[16]= "code,libelle,sortorder";
$tabfield[17]= "code,label,accountancy_code";
$tabfield[18]= "code,libelle,tracking";
$tabfield[19]= "code,libelle";
$tabfield[20]= "code,libelle";
$tabfield[21]= "code,label";
$tabfield[22]= "code,label";
$tabfield[23]= "country_id,country,taux,accountancy_code_sell,accountancy_code_buy,note";
$tabfield[24]= "code,label";
$tabfield[25]= "label,type_template,position,topic,content";
$tabfield[26]= "code,label,short_label";
$tabfield[27]= "code,libelle";
$tabfield[28]= "code,label,delay,newByMonth,country_id,country";
$tabfield[29]= "code,label,percent,position";

// Nom des champs d'edition pour modification d'un enregistrement
$tabfieldvalue=array();
$tabfieldvalue[1] = "code,libelle,country";
$tabfieldvalue[2] = "code,libelle,region";   // "code,libelle,region"
$tabfieldvalue[3] = "code,libelle,country";
$tabfieldvalue[4] = "code,label";
$tabfieldvalue[5] = "code,label";
$tabfieldvalue[6] = "code,libelle,type,color,position";
$tabfieldvalue[7] = "code,libelle,country,accountancy_code,deductible";
$tabfieldvalue[8] = "code,libelle,country";
$tabfieldvalue[9] = "code,label,unicode";
$tabfieldvalue[10]= "country,taux,recuperableonly,localtax1_type,localtax1,localtax2_type,localtax2,accountancy_code_sell,accountancy_code_buy,note";
$tabfieldvalue[11]= "element,source,code,libelle";
$tabfieldvalue[12]= "code,libelle,libelle_facture,nbjour,fdm,decalage,sortorder";
$tabfieldvalue[13]= "code,libelle,type,accountancy_code";
$tabfieldvalue[14]= "code,libelle,price,organization,country";
$tabfieldvalue[15]= "code,libelle,width,height,unit";
$tabfieldvalue[16]= "code,libelle,sortorder";
$tabfieldvalue[17]= "code,label,accountancy_code";
$tabfieldvalue[18]= "code,libelle,tracking";
$tabfieldvalue[19]= "code,libelle";
$tabfieldvalue[20]= "code,libelle";
$tabfieldvalue[21]= "code,label";
$tabfieldvalue[22]= "code,label";
$tabfieldvalue[23]= "country,taux,accountancy_code_sell,accountancy_code_buy,note";
$tabfieldvalue[24]= "code,label";
$tabfieldvalue[25]= "label,type_template,position,topic,content";
$tabfieldvalue[26]= "code,label,short_label";
$tabfieldvalue[27]= "code,libelle";
$tabfieldvalue[28]= "code,label,delay,newByMonth,country";
$tabfieldvalue[29]= "code,label,percent,position";

// Nom des champs dans la table pour insertion d'un enregistrement
$tabfieldinsert=array();
$tabfieldinsert[1] = "code,libelle,fk_pays";
$tabfieldinsert[2] = "code_departement,nom,fk_region";
$tabfieldinsert[3] = "code_region,nom,fk_pays";
$tabfieldinsert[4] = "code,label";
$tabfieldinsert[5] = "code,label";
$tabfieldinsert[6] = "code,libelle,type,color,position";
$tabfieldinsert[7] = "code,libelle,fk_pays,accountancy_code,deductible";
$tabfieldinsert[8] = "code,libelle,fk_country";
$tabfieldinsert[9] = "code_iso,label,unicode";
$tabfieldinsert[10]= "fk_pays,taux,recuperableonly,localtax1_type,localtax1,localtax2_type,localtax2,accountancy_code_sell,accountancy_code_buy,note";
$tabfieldinsert[11]= "element,source,code,libelle";
$tabfieldinsert[12]= "code,libelle,libelle_facture,nbjour,fdm,decalage,sortorder";
$tabfieldinsert[13]= "code,libelle,type,accountancy_code";
$tabfieldinsert[14]= "code,libelle,price,organization,fk_pays";
$tabfieldinsert[15]= "code,label,width,height,unit";
$tabfieldinsert[16]= "code,label,sortorder";
$tabfieldinsert[17]= "code,label,accountancy_code";
$tabfieldinsert[18]= "code,libelle,tracking";
$tabfieldinsert[19]= "code,libelle";
$tabfieldinsert[20]= "code,libelle";
$tabfieldinsert[21]= "code,label";
$tabfieldinsert[22]= "code,label";
$tabfieldinsert[23]= "fk_pays,taux,accountancy_code_sell,accountancy_code_buy,note";
$tabfieldinsert[24]= "code,label";
$tabfieldinsert[25]= "label,type_template,position,topic,content";
$tabfieldinsert[26]= "code,label,short_label";
$tabfieldinsert[27]= "code,libelle";
$tabfieldinsert[28]= "code,label,delay,newByMonth,fk_country";
$tabfieldinsert[29]= "code,label,percent,position";

// Nom du rowid si le champ n'est pas de type autoincrement
// Example: "" if id field is "rowid" and has autoincrement on
//          "nameoffield" if id field is not "rowid" or has not autoincrement on
$tabrowid=array();
$tabrowid[1] = "";
$tabrowid[2] = "";
$tabrowid[3] = "";
$tabrowid[4] = "rowid";
$tabrowid[5] = "rowid";
$tabrowid[6] = "id";
$tabrowid[7] = "id";
$tabrowid[8] = "id";
$tabrowid[9] = "code_iso";
$tabrowid[10]= "";
$tabrowid[11]= "rowid";
$tabrowid[12]= "rowid";
$tabrowid[13]= "id";
$tabrowid[14]= "";
$tabrowid[15]= "";
$tabrowid[16]= "code";
$tabrowid[17]= "id";
$tabrowid[18]= "rowid";
$tabrowid[19]= "id";
$tabrowid[20]= "";
$tabrowid[21]= "rowid";
$tabrowid[22]= "rowid";
$tabrowid[23]= "";
$tabrowid[24]= "";
$tabrowid[25]= "";
$tabrowid[26]= "";
$tabrowid[27]= "id";
$tabrowid[28]= "";
$tabrowid[29]= "";

// Condition to show dictionary in setup page
$tabcond=array();
$tabcond[1] = true;
$tabcond[2] = true;
$tabcond[3] = true;
$tabcond[4] = true;
$tabcond[5] = (! empty($conf->societe->enabled) || ! empty($conf->adherent->enabled));
$tabcond[6] = ! empty($conf->agenda->enabled);
$tabcond[7] = ! empty($conf->tax->enabled);
$tabcond[8] = ! empty($conf->societe->enabled);
$tabcond[9] = true;
$tabcond[10]= true;
$tabcond[11]= true;
$tabcond[12]= (! empty($conf->commande->enabled) || ! empty($conf->propal->enabled) || ! empty($conf->facture->enabled) || ! empty($conf->fournisseur->enabled));
$tabcond[13]= (! empty($conf->commande->enabled) || ! empty($conf->propal->enabled) || ! empty($conf->facture->enabled) || ! empty($conf->fournisseur->enabled));
$tabcond[14]= (! empty($conf->product->enabled) && ! empty($conf->ecotax->enabled));
$tabcond[15]= true;
$tabcond[16]= (! empty($conf->societe->enabled) && empty($conf->global->SOCIETE_DISABLE_PROSPECTS));
$tabcond[17]= (! empty($conf->deplacement->enabled) || ! empty($conf->expensereport->enabled));
$tabcond[18]= ! empty($conf->expedition->enabled);
$tabcond[19]= ! empty($conf->societe->enabled);
$tabcond[20]= ! empty($conf->fournisseur->enabled);
$tabcond[21]= ! empty($conf->propal->enabled);
$tabcond[22]= (! empty($conf->commande->enabled) || ! empty($conf->propal->enabled));
$tabcond[23]= true;
$tabcond[24]= ! empty($conf->resource->enabled);
$tabcond[25]= true; // && ! empty($conf->global->MAIN_EMAIL_EDIT_TEMPLATE_FROM_DIC);
$tabcond[26]= ! empty($conf->product->enabled);
$tabcond[27]= ! empty($conf->societe->enabled);
$tabcond[28]= ! empty($conf->holiday->enabled);
$tabcond[29]= ! empty($conf->projet->enabled);

// List of help for fields
$tabhelp=array();
$tabhelp[1]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[2]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[3]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[4]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[5]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[6]  = array('code'=>$langs->trans("EnterAnyCode"), 'position'=>$langs->trans("PositionIntoComboList"));
$tabhelp[7]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[8]  = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[9]  = array('code'=>$langs->trans("EnterAnyCode"), 'unicode'=>$langs->trans("UnicodeCurrency"));
$tabhelp[10] = array('taux'=>$langs->trans("SellTaxRate"), 'recuperableonly'=>$langs->trans("RecuperableOnly"), 'localtax1_type'=>$langs->trans("LocalTaxDesc"), 'localtax2_type'=>$langs->trans("LocalTaxDesc"));
$tabhelp[11] = array();
$tabhelp[12] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[13] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[14] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[15] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[16] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[17] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[18] = array('code'=>$langs->trans("EnterAnyCode"), 'tracking'=>$langs->trans("UrlTrackingDesc"));
$tabhelp[19] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[20] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[21] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[22] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[23] = array();
$tabhelp[24] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[25] = array('type_template'=>$langs->trans("TemplateForElement"),'private'=>$langs->trans("TemplateIsVisibleByOwnerOnly"), 'position'=>$langs->trans("PositionIntoComboList"));
$tabhelp[26] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[27] = array('code'=>$langs->trans("EnterAnyCode"));
$tabhelp[28] = array('delay'=>$langs->trans("MinimumNoticePeriod"), 'newByMonth'=>$langs->trans("NbAddedAutomatically"));
$tabhelp[29] = array('code'=>$langs->trans("EnterAnyCode"), 'percent'=>$langs->trans("OpportunityPercent"), 'position'=>$langs->trans("PositionIntoComboList"));

// List of check for fields (NOT USED YET)
$tabfieldcheck=array();
$tabfieldcheck[1]  = array();
$tabfieldcheck[2]  = array();
$tabfieldcheck[3]  = array();
$tabfieldcheck[4]  = array();
$tabfieldcheck[5]  = array();
$tabfieldcheck[6]  = array();
$tabfieldcheck[7]  = array();
$tabfieldcheck[8]  = array();
$tabfieldcheck[9]  = array();
$tabfieldcheck[10] = array();
$tabfieldcheck[11] = array();
$tabfieldcheck[12] = array();
$tabfieldcheck[13] = array();
$tabfieldcheck[14] = array();
$tabfieldcheck[15] = array();
$tabfieldcheck[16] = array();
$tabfieldcheck[17] = array();
$tabfieldcheck[18] = array();
$tabfieldcheck[19] = array();
$tabfieldcheck[20] = array();
$tabfieldcheck[21] = array();
$tabfieldcheck[22] = array();
$tabfieldcheck[23] = array();
$tabfieldcheck[24] = array();
$tabfieldcheck[25] = array();
$tabfieldcheck[26] = array();
$tabfieldcheck[27] = array();
$tabfieldcheck[28] = array();
$tabfieldcheck[29] = array();

// Complete all arrays with entries found into modules
complete_dictionary_with_modules($taborder,$tabname,$tablib,$tabsql,$tabsqlsort,$tabfield,$tabfieldvalue,$tabfieldinsert,$tabrowid,$tabcond,$tabhelp,$tabfieldcheck);


// Define elementList and sourceList (used for dictionary type of contacts "llx_c_type_contact")
$elementList = array();
$sourceList=array();
if ($id == 11)
{
	$langs->load("orders");
	$langs->load("contracts");
	$langs->load("projects");
	$langs->load("propal");
	$langs->load("bills");
	$langs->load("interventions");
	$elementList = array(
			''				    => '',
            'societe'           => $langs->trans('ThirdParty'),
//			'proposal'          => $langs->trans('Proposal'),
//			'order'             => $langs->trans('Order'),
//			'invoice'           => $langs->trans('Bill'),
			'invoice_supplier'  => $langs->trans('SupplierBill'),
			'order_supplier'    => $langs->trans('SupplierOrder'),
//			'intervention'      => $langs->trans('InterventionCard'),
//			'contract'          => $langs->trans('Contract'),
			'project'           => $langs->trans('Project'),
			'project_task'      => $langs->trans('Task'),
			'agenda'			=> $langs->trans('Agenda'),
			// old deprecated
			'contrat'           => $langs->trans('Contract'),
			'propal'            => $langs->trans('Proposal'),
			'commande'          => $langs->trans('Order'),
			'facture'           => $langs->trans('Bill'),
//			'facture_fourn'     => $langs->trans('SupplierBill'),
			'fichinter'         => $langs->trans('InterventionCard')
	);
	if (! empty($conf->global->MAIN_SUPPORT_SHARED_CONTACT_BETWEEN_THIRDPARTIES)) $elementList["societe"] = $langs->trans('ThirdParty');

	complete_elementList_with_modules($elementList);

	asort($elementList);
	$sourceList = array(
			'internal' => $langs->trans('Internal'),
			'external' => $langs->trans('External')
	);
}
if ($id == 25)
{
	// We save list of template type Dolibarr can manage. This list can found by a grep into code on "->param['models']"
	$elementList = array(
			'propal_send'    => $langs->trans('MailToSendProposal'),
			'order_send'     => $langs->trans('MailToSendOrder'),
			'facture_send'   => $langs->trans('MailToSendInvoice'),

			'shipping_send'  => $langs->trans('MailToSendShipment'),
			'fichinter_send' => $langs->trans('MailToSendIntervention'),

			'askpricesupplier_send'  => $langs->trans('MailToSendSupplierRequestForQuotation'),
			'order_supplier_send'    => $langs->trans('MailToSendSupplierOrder'),
			'invoice_supplier_send'  => $langs->trans('MailToSendSupplierInvoice'),

			'thirdparty'    => $langs->trans('MailToThirdparty')
	);
}

// Define localtax_typeList (used for dictionary "llx_c_tva")
$localtax_typeList = array();
if ($id == 10)
{
	$localtax_typeList = array(
			"0" => $langs->trans("No"),
			"1" => $langs->trans("Yes").' ('.$langs->trans("Type")." 1)",	//$langs->trans("%ageOnAllWithoutVAT"),
			"2" => $langs->trans("Yes").' ('.$langs->trans("Type")." 2)",	//$langs->trans("%ageOnAllBeforeVAT"),
			"3" => $langs->trans("Yes").' ('.$langs->trans("Type")." 3)",	//$langs->trans("%ageOnProductsWithoutVAT"),
			"4" => $langs->trans("Yes").' ('.$langs->trans("Type")." 4)",	//$langs->trans("%ageOnProductsBeforeVAT"),
			"5" => $langs->trans("Yes").' ('.$langs->trans("Type")." 5)",	//$langs->trans("%ageOnServiceWithoutVAT"),
			"6" => $langs->trans("Yes").' ('.$langs->trans("Type")." 6)"	//$langs->trans("%ageOnServiceBeforeVAT"),
	);
}


// Actions ajout ou modification d'une entree dans un dictionnaire de donnee
if (GETPOST('actionadd') || GETPOST('actionmodify'))
{
    $listfield=explode(',',$tabfield[$id]);
    $listfieldinsert=explode(',',$tabfieldinsert[$id]);
    $listfieldmodify=explode(',',$tabfieldinsert[$id]);
    $listfieldvalue=explode(',',$tabfieldvalue[$id]);

    // Check that all fields are filled
    $ok=1;
    foreach ($listfield as $f => $value)
    {
        if ($value == 'country_id' && in_array($tablib[$id],array('DictionaryVAT','DictionaryRegion','DictionaryCompanyType','DictionaryHolidayTypes'))) continue;		// For some pages, country is not mandatory
    	if ($value == 'country' && in_array($tablib[$id],array('DictionaryCanton','DictionaryCompanyType'))) continue;		// For some pages, country is not mandatory
        if ($value == 'localtax1' && empty($_POST['localtax1_type'])) continue;
        if ($value == 'localtax2' && empty($_POST['localtax2_type'])) continue;
        if ($value == 'color' && empty($_POST['color'])) continue;
        if ((! isset($_POST[$value]) || $_POST[$value]=='')
        	&& (! in_array($listfield[$f], array('decalage','module','accountancy_code','accountancy_code_sell','accountancy_code_buy')))  // Fields that are not mandatory
		)
        {
            $ok=0;
            $fieldnamekey=$listfield[$f];
            // We take translate key of field
            if ($fieldnamekey == 'libelle' || ($fieldnamekey == 'label'))  $fieldnamekey='Label';
            if ($fieldnamekey == 'libelle_facture') $fieldnamekey = 'LabelOnDocuments';
            if ($fieldnamekey == 'nbjour')   $fieldnamekey='NbOfDays';
            if ($fieldnamekey == 'decalage') $fieldnamekey='Offset';
            if ($fieldnamekey == 'module')   $fieldnamekey='Module';
            if ($fieldnamekey == 'code') $fieldnamekey = 'Code';
            if ($fieldnamekey == 'note') $fieldnamekey = 'Note';
            if ($fieldnamekey == 'taux') $fieldnamekey = 'Rate';
            if ($fieldnamekey == 'type') $fieldnamekey = 'Type';
            if ($fieldnamekey == 'position') $fieldnamekey = 'Position';
            if ($fieldnamekey == 'unicode') $fieldnamekey = 'Unicode';
            if ($fieldnamekey == 'deductible') $fieldnamekey = 'Deductible';
            if ($fieldnamekey == 'sortorder') $fieldnamekey = 'SortOrder';

            setEventMessage($langs->transnoentities("ErrorFieldRequired", $langs->transnoentities($fieldnamekey)),'errors');
        }
    }
    // Other checks
    if ($tabname[$id] == MAIN_DB_PREFIX."c_actioncomm" && isset($_POST["type"]) && in_array($_POST["type"],array('system','systemauto'))) {
        $ok=0;
        setEventMessage($langs->transnoentities('ErrorReservedTypeSystemSystemAuto'),'errors');
    }
    if (isset($_POST["code"]))
    {
    	if ($_POST["code"]=='0')
    	{
        	$ok=0;
    		setEventMessage($langs->transnoentities('ErrorCodeCantContainZero'),'errors');
        }
        /*if (!is_numeric($_POST['code']))	// disabled, code may not be in numeric base
    	{
	    	$ok = 0;
	    	$msg .= $langs->transnoentities('ErrorFieldFormat', $langs->transnoentities('Code')).'<br />';
	    }*/
    }
    if (isset($_POST["country"]) && ($_POST["country"]=='0') && ($id != 2))
    {
    	if (in_array($tablib[$id],array('DictionaryCompanyType','DictionaryHolidayTypes')))	// Field country is no mandatory for such dictionaries
    	{
    		$_POST["country"]='';
    	}
    	else
    	{
        	$ok=0;
        	setEventMessage($langs->transnoentities("ErrorFieldRequired",$langs->transnoentities("Country")),'errors');
    	}
    }

	// Clean some parameters
    if (isset($_POST["localtax1"]) && empty($_POST["localtax1"])) $_POST["localtax1"]='0';	// If empty, we force to 0
    if (isset($_POST["localtax2"]) && empty($_POST["localtax2"])) $_POST["localtax2"]='0';	// If empty, we force to 0

    // Si verif ok et action add, on ajoute la ligne
    if ($ok && GETPOST('actionadd'))
    {
        if ($tabrowid[$id])
        {
            // Recupere id libre pour insertion
            $newid=0;
            $sql = "SELECT max(".$tabrowid[$id].") newid from ".$tabname[$id];
            $result = $db->query($sql);
            if ($result)
            {
                $obj = $db->fetch_object($result);
                $newid=($obj->newid + 1);

            } else {
                dol_print_error($db);
            }
        }

        // Add new entry
        $sql = "INSERT INTO ".$tabname[$id]." (";
        // List of fields
        if ($tabrowid[$id] && ! in_array($tabrowid[$id],$listfieldinsert))
        	$sql.= $tabrowid[$id].",";
        $sql.= $tabfieldinsert[$id];
        $sql.=",active)";
        $sql.= " VALUES(";

        // List of values
        if ($tabrowid[$id] && ! in_array($tabrowid[$id],$listfieldinsert))
        	$sql.= $newid.",";
        $i=0;
        foreach ($listfieldinsert as $f => $value)
        {
            if ($value == 'price' || preg_match('/^amount/i',$value) || preg_match('/^localtax/i',$value) || $value == 'taux') {
            	$_POST[$listfieldvalue[$i]] = price2num($_POST[$listfieldvalue[$i]],'MU');
            }
            else if ($value == 'entity') {
            	$_POST[$listfieldvalue[$i]] = $conf->entity;
            }
            if ($i) $sql.=",";
            if ($_POST[$listfieldvalue[$i]] == '') $sql.="null";
            else $sql.="'".$db->escape($_POST[$listfieldvalue[$i]])."'";
            $i++;
        }
        $sql.=",1)";

        dol_syslog("actionadd", LOG_DEBUG);
        $result = $db->query($sql);
        if ($result)	// Add is ok
        {
            setEventMessage($langs->transnoentities("RecordSaved"));
        	$_POST=array('id'=>$id);	// Clean $_POST array, we keep only
        }
        else
        {
            if ($db->errno() == 'DB_ERROR_RECORD_ALREADY_EXISTS') {
                setEventMessage($langs->transnoentities("ErrorRecordAlreadyExists"),'errors');
            }
            else {
                dol_print_error($db);
            }
        }
    }

    // Si verif ok et action modify, on modifie la ligne
    if ($ok && GETPOST('actionmodify'))
    {
        if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
        else { $rowidcol="rowid"; }

        // Modify entry
        $sql = "UPDATE ".$tabname[$id]." SET ";
        // Modifie valeur des champs
        if ($tabrowid[$id] && ! in_array($tabrowid[$id],$listfieldmodify))
        {
            $sql.= $tabrowid[$id]."=";
            $sql.= "'".$db->escape($rowid)."', ";
        }
        $i = 0;
        foreach ($listfieldmodify as $field)
        {
            if ($field == 'price' || preg_match('/^amount/i',$field) || preg_match('/^localtax/i',$field) || $field == 'taux') {
            	$_POST[$listfieldvalue[$i]] = price2num($_POST[$listfieldvalue[$i]],'MU');
            }
            else if ($field == 'entity') {
            	$_POST[$listfieldvalue[$i]] = $conf->entity;
            }
            if ($i) $sql.=",";
            $sql.= $field."=";
            if ($_POST[$listfieldvalue[$i]] == '') $sql.="null";
            else $sql.="'".$db->escape($_POST[$listfieldvalue[$i]])."'";
            $i++;
        }
        $sql.= " WHERE ".$rowidcol." = '".$rowid."'";

        dol_syslog("actionmodify", LOG_DEBUG);
        //print $sql;
        $resql = $db->query($sql);
        if (! $resql)
        {
            setEventMessage($db->error(),'errors');
        }
    }
    //$_GET["id"]=GETPOST('id', 'int');       // Force affichage dictionnaire en cours d'edition
}

if (GETPOST('actioncancel'))
{
    //$_GET["id"]=GETPOST('id', 'int');       // Force affichage dictionnaire en cours d'edition
}

if ($action == 'confirm_delete' && $confirm == 'yes')       // delete
{
    if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
    else { $rowidcol="rowid"; }

    $sql = "DELETE from ".$tabname[$id]." WHERE ".$rowidcol."='".$rowid."'";

    dol_syslog("delete", LOG_DEBUG);
    $result = $db->query($sql);
    if (! $result)
    {
        if ($db->errno() == 'DB_ERROR_CHILD_EXISTS')
        {
            setEventMessage($langs->transnoentities("ErrorRecordIsUsedByChild"),'errors');
        }
        else
        {
            dol_print_error($db);
        }
    }
}

// activate
if ($action == $acts[0])
{
    if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
    else { $rowidcol="rowid"; }

    if ($rowid) {
        $sql = "UPDATE ".$tabname[$id]." SET active = 1 WHERE ".$rowidcol."='".$rowid."'";
    }
    elseif ($_GET["code"]) {
        $sql = "UPDATE ".$tabname[$id]." SET active = 1 WHERE code='".$_GET["code"]."'";
    }

    $result = $db->query($sql);
    if (!$result)
    {
        dol_print_error($db);
    }
}

// disable
if ($action == $acts[1])
{
    if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
    else { $rowidcol="rowid"; }

    if ($rowid) {
        $sql = "UPDATE ".$tabname[$id]." SET active = 0 WHERE ".$rowidcol."='".$rowid."'";
    }
    elseif ($_GET["code"]) {
        $sql = "UPDATE ".$tabname[$id]." SET active = 0 WHERE code='".$_GET["code"]."'";
    }

    $result = $db->query($sql);
    if (!$result)
    {
        dol_print_error($db);
    }
}

// favorite
if ($action == 'activate_favorite')
{
    if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
    else { $rowidcol="rowid"; }

    if ($rowid) {
        $sql = "UPDATE ".$tabname[$id]." SET favorite = 1 WHERE ".$rowidcol."='".$rowid."'";
    }
    elseif ($_GET["code"]) {
        $sql = "UPDATE ".$tabname[$id]." SET favorite = 1 WHERE code='".$_GET["code"]."'";
    }

    $result = $db->query($sql);
    if (!$result)
    {
        dol_print_error($db);
    }
}

// disable favorite
if ($action == 'disable_favorite')
{
    if ($tabrowid[$id]) { $rowidcol=$tabrowid[$id]; }
    else { $rowidcol="rowid"; }

    if ($rowid) {
        $sql = "UPDATE ".$tabname[$id]." SET favorite = 0 WHERE ".$rowidcol."='".$rowid."'";
    }
    elseif ($_GET["code"]) {
        $sql = "UPDATE ".$tabname[$id]." SET favorite = 0 WHERE code='".$_GET["code"]."'";
    }

    $result = $db->query($sql);
    if (!$result)
    {
        dol_print_error($db);
    }
}


/*
 * View
 */

$form = new Form($db);
$formadmin=new FormAdmin($db);

llxHeader();

$titre=$langs->trans("DictionarySetup");
$linkback='';
if ($id)
{
    $titre.=' - '.$langs->trans($tablib[$id]);
    $linkback='<a href="'.$_SERVER['PHP_SELF'].'">'.$langs->trans("BackToDictionaryList").'</a>';
}
print load_fiche_titre($titre,$linkback,'title_setup');

if (empty($id))
{
    print $langs->trans("DictionaryDesc");
    print " ".$langs->trans("OnlyActiveElementsAreShown")."<br>\n";
}
print "<br>\n";


// Confirmation de la suppression de la ligne
if ($action == 'delete')
{
    print $form->formconfirm($_SERVER["PHP_SELF"].'?'.($page?'page='.$page.'&':'').'sortfield='.$sortfield.'&sortorder='.$sortorder.'&rowid='.$rowid.'&code='.$_GET["code"].'&id='.$id, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_delete','',0,1);
}
//var_dump($elementList);

/*
 * Show a dictionary
 */
if ($id)
{
    // Complete requete recherche valeurs avec critere de tri
    $sql=$tabsql[$id];

    if ($sortfield)
    {
        // If sort order is "country", we use country_code instead
    	if ($sortfield == 'country') $sortfield='country_code';
        $sql.= " ORDER BY ".$sortfield;
        if ($sortorder)
        {
            $sql.=" ".strtoupper($sortorder);
        }
        $sql.=", ";
        // Clear the required sort criteria for the tabsqlsort to be able to force it with selected value
        $tabsqlsort[$id]=preg_replace('/([a-z]+\.)?'.$sortfield.' '.$sortorder.',/i','',$tabsqlsort[$id]);
        $tabsqlsort[$id]=preg_replace('/([a-z]+\.)?'.$sortfield.',/i','',$tabsqlsort[$id]);
    }
    else {
        $sql.=" ORDER BY ";
    }
    $sql.=$tabsqlsort[$id];
    $sql.=$db->plimit($listlimit+1,$offset);
    //print $sql;

    $fieldlist=explode(',',$tabfield[$id]);

    print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<table class="noborder" width="100%">';

    // Form to add a new line
    if ($tabname[$id])
    {
        $alabelisused=0;
        $var=false;

        $fieldlist=explode(',',$tabfield[$id]);

        // Line for title
        print '<tr class="liste_titre">';
        foreach ($fieldlist as $field => $value)
        {
            // Determine le nom du champ par rapport aux noms possibles
            // dans les dictionnaires de donnees
            $valuetoshow=ucfirst($fieldlist[$field]);   // Par defaut
            $valuetoshow=$langs->trans($valuetoshow);   // try to translate
            $align="left";
            if ($fieldlist[$field]=='source')          { $valuetoshow=$langs->trans("Contact"); }
            if ($fieldlist[$field]=='price')           { $valuetoshow=$langs->trans("PriceUHT"); }
            if ($fieldlist[$field]=='taux')            {
				if ($tabname[$id] != MAIN_DB_PREFIX."c_revenuestamp") $valuetoshow=$langs->trans("Rate");
				else $valuetoshow=$langs->trans("Amount");
				$align='right';
            }
            if ($fieldlist[$field]=='localtax1_type')  { $valuetoshow=$langs->trans("UseLocalTax")." 2"; $align="center"; $sortable=0; }
            if ($fieldlist[$field]=='localtax1')       { $valuetoshow=$langs->trans("Rate")." 2";}
            if ($fieldlist[$field]=='localtax2_type')  { $valuetoshow=$langs->trans("UseLocalTax")." 3"; $align="center"; $sortable=0; }
            if ($fieldlist[$field]=='localtax2')       { $valuetoshow=$langs->trans("Rate")." 3";}
            if ($fieldlist[$field]=='organization')    { $valuetoshow=$langs->trans("Organization"); }
            if ($fieldlist[$field]=='lang')            { $valuetoshow=$langs->trans("Language"); }
            if ($fieldlist[$field]=='type')            {
				if ($tabname[$id] == MAIN_DB_PREFIX."c_paiement") $valuetoshow=$form->textwithtooltip($langs->trans("Type"),$langs->trans("TypePaymentDesc"),2,1,img_help(1,''));
				else $valuetoshow=$langs->trans("Type");
            }
            if ($fieldlist[$field]=='code')            { $valuetoshow=$langs->trans("Code"); }
            if ($fieldlist[$field]=='libelle' || $fieldlist[$field]=='label')
            {
            	$valuetoshow=$langs->trans("Label");
            	if ($id != 25) $valuetoshow.="*";
            }
            if ($fieldlist[$field]=='libelle_facture') { $valuetoshow=$langs->trans("LabelOnDocuments")."*"; }
            if ($fieldlist[$field]=='country')         {
                if (in_array('region_id',$fieldlist)) { print '<td>&nbsp;</td>'; continue; }		// For region page, we do not show the country input
                $valuetoshow=$langs->trans("Country");
            }
            if ($fieldlist[$field]=='recuperableonly') { $valuetoshow=$langs->trans("NPR"); $align="center"; }
            if ($fieldlist[$field]=='nbjour')          { $valuetoshow=$langs->trans("NbOfDays"); }
            if ($fieldlist[$field]=='fdm')             { $valuetoshow=$langs->trans("AtEndOfMonth"); }
            if ($fieldlist[$field]=='decalage')        { $valuetoshow=$langs->trans("Offset"); }
            if ($fieldlist[$field]=='width')           { $valuetoshow=$langs->trans("Width"); }
            if ($fieldlist[$field]=='height')          { $valuetoshow=$langs->trans("Height"); }
            if ($fieldlist[$field]=='unit')            { $valuetoshow=$langs->trans("MeasuringUnit"); }
            if ($fieldlist[$field]=='region_id' || $fieldlist[$field]=='country_id') { $valuetoshow=''; }
            if ($fieldlist[$field]=='accountancy_code'){ $valuetoshow=$langs->trans("AccountancyCode"); }
            if ($fieldlist[$field]=='accountancy_code_sell'){ $valuetoshow=$langs->trans("AccountancyCodeSell"); }
            if ($fieldlist[$field]=='accountancy_code_buy'){ $valuetoshow=$langs->trans("AccountancyCodeBuy"); }
            if ($fieldlist[$field]=='pcg_version' || $fieldlist[$field]=='fk_pcg_version') { $valuetoshow=$langs->trans("Pcg_version"); }
            if ($fieldlist[$field]=='account_parent')  { $valuetoshow=$langs->trans("Accountparent"); }
            if ($fieldlist[$field]=='pcg_type')        { $valuetoshow=$langs->trans("Pcg_type"); }
            if ($fieldlist[$field]=='pcg_subtype')     { $valuetoshow=$langs->trans("Pcg_subtype"); }
            if ($fieldlist[$field]=='sortorder')       { $valuetoshow=$langs->trans("SortOrder"); }
	        if ($fieldlist[$field]=='short_label')     { $valuetoshow=$langs->trans("ShortLabel"); }
            if ($fieldlist[$field]=='type_template')   { $valuetoshow=$langs->trans("TypeOfTemplate"); }

            if ($id == 2)	// Special cas for state page
            {
            	if ($fieldlist[$field]=='region_id') { $valuetoshow='&nbsp;'; $showfield=1; }
	            if ($fieldlist[$field]=='region') { $valuetoshow=$langs->trans("Country").'/'.$langs->trans("Region"); $showfield=1; }
            }

            if ($valuetoshow != '')
            {
                print '<td align="'.$align.'">';
            	if (! empty($tabhelp[$id][$value]) && preg_match('/^http(s*):/i',$tabhelp[$id][$value])) print '<a href="'.$tabhelp[$id][$value].'" target="_blank">'.$valuetoshow.' '.img_help(1,$valuetoshow).'</a>';
            	else if (! empty($tabhelp[$id][$value])) print $form->textwithpicto($valuetoshow,$tabhelp[$id][$value]);
            	else print $valuetoshow;
                print '</td>';
             }
             if ($fieldlist[$field]=='libelle' || $fieldlist[$field]=='label') $alabelisused=1;
        }

        if ($id == 4) print '<td></td>';
        print '<td colspan="4">';
        print '<input type="hidden" name="id" value="'.$id.'">';
        print '</td>';
        print '</tr>';

        // Line to enter new values
        print "<tr ".$bcnd[$var].">";

        $obj = new stdClass();
        // If data was already input, we define them in obj to populate input fields.
        if (GETPOST('actionadd'))
        {
            foreach ($fieldlist as $key=>$val)
            {
                if (GETPOST($val))
                	$obj->$val=GETPOST($val);
            }
        }

        $tmpaction = 'create';
        $parameters=array('fieldlist'=>$fieldlist, 'tabname'=>$tabname[$id]);
        $reshook=$hookmanager->executeHooks('createDictionaryFieldlist',$parameters, $obj, $tmpaction);    // Note that $action and $object may have been modified by some hooks
        $error=$hookmanager->error; $errors=$hookmanager->errors;

        if ($id == 3) unset($fieldlist[2]);

        if (empty($reshook))
        {
        	if ($tabname[$id] == MAIN_DB_PREFIX.'c_email_templates' && $action == 'edit')
        	{
				fieldList($fieldlist,$obj,$tabname[$id],'hide');
        	}
        	else
        	{
        		fieldList($fieldlist,$obj,$tabname[$id],'add');
        	}
        }

        if ($id == 4) print '<td></td>';
        print '<td colspan="3" align="right">';
        if ($tabname[$id] != MAIN_DB_PREFIX.'c_email_templates' || $action != 'edit')
        {
        	print '<input type="submit" class="button" name="actionadd" value="'.$langs->trans("Add").'">';
        }
        print '</td>';
        print "</tr>";

        $colspan=count($fieldlist)+2;
        if ($id == 4) $colspan++;

        if (! empty($alabelisused) && $id != 25)  // If there is one label among fields, we show legend of *
        {
        	print '<tr><td colspan="'.$colspan.'">* '.$langs->trans("LabelUsedByDefault").'.</td></tr>';
        }
        print '<tr><td colspan="'.$colspan.'">&nbsp;</td></tr>';	// Keep &nbsp; to have a line with enough height
    }

    print '</form>';



    // List of available values in database
    dol_syslog("htdocs/admin/dict", LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $num = $db->num_rows($resql);
        $i = 0;
        $var=true;
        if ($num)
        {
            // There is several pages
            if ($num > $listlimit)
            {
                print '<tr class="none"><td align="right" colspan="'.(3+count($fieldlist)).'">';
                print_fleche_navigation($page, $_SERVER["PHP_SELF"], '&id='.$id, ($num > $listlimit), '<li class="pagination"><span>'.$langs->trans("Page").' '.($page+1).'</span></li>');
                print '</td></tr>';
            }

            // Title of lines
            print '<tr class="liste_titre">';
            foreach ($fieldlist as $field => $value)
            {
                // Determine le nom du champ par rapport aux noms possibles
                // dans les dictionnaires de donnees
                $showfield=1;							  	// Par defaut
                $align="left";
                $sortable=1;
                $valuetoshow='';
                /*
                $tmparray=getLabelOfField($fieldlist[$field]);
                $showfield=$tmp['showfield'];
                $valuetoshow=$tmp['valuetoshow'];
                $align=$tmp['align'];
                $sortable=$tmp['sortable'];
				*/
                $valuetoshow=ucfirst($fieldlist[$field]);   // Par defaut
                $valuetoshow=$langs->trans($valuetoshow);   // try to translate
                if ($fieldlist[$field]=='source')          { $valuetoshow=$langs->trans("Contact"); }
                if ($fieldlist[$field]=='price')           { $valuetoshow=$langs->trans("PriceUHT"); }
                if ($fieldlist[$field]=='taux')            {
					if ($tabname[$id] != MAIN_DB_PREFIX."c_revenuestamp") $valuetoshow=$langs->trans("Rate");
					else $valuetoshow=$langs->trans("Amount");
					$align='right';
	            }
                if ($fieldlist[$field]=='localtax1_type')  { $valuetoshow=$langs->trans("UseLocalTax")." 2"; $align="center"; $sortable=0; }
                if ($fieldlist[$field]=='localtax1')       { $valuetoshow=$langs->trans("Rate")." 2"; $sortable=0; }
                if ($fieldlist[$field]=='localtax2_type')  { $valuetoshow=$langs->trans("UseLocalTax")." 3"; $align="center"; $sortable=0; }
                if ($fieldlist[$field]=='localtax2')       { $valuetoshow=$langs->trans("Rate")." 3"; $sortable=0; }
                if ($fieldlist[$field]=='organization')    { $valuetoshow=$langs->trans("Organization"); }
                if ($fieldlist[$field]=='lang')            { $valuetoshow=$langs->trans("Language"); }
                if ($fieldlist[$field]=='type')            { $valuetoshow=$langs->trans("Type"); }
                if ($fieldlist[$field]=='code')            { $valuetoshow=$langs->trans("Code"); }
                if ($fieldlist[$field]=='libelle' || $fieldlist[$field]=='label')
                {
                	$valuetoshow=$langs->trans("Label");
                   	if ($id != 25) $valuetoshow.="*";
                }
                if ($fieldlist[$field]=='libelle_facture') { $valuetoshow=$langs->trans("LabelOnDocuments")."*"; }
                if ($fieldlist[$field]=='country')         { $valuetoshow=$langs->trans("Country"); }
                if ($fieldlist[$field]=='recuperableonly') { $valuetoshow=$langs->trans("NPR"); $align="center"; }
                if ($fieldlist[$field]=='nbjour')          { $valuetoshow=$langs->trans("NbOfDays"); }
                if ($fieldlist[$field]=='fdm')             { $valuetoshow=$langs->trans("AtEndOfMonth"); }
                if ($fieldlist[$field]=='decalage')        { $valuetoshow=$langs->trans("Offset"); }
                if ($fieldlist[$field]=='width')           { $valuetoshow=$langs->trans("Width"); }
                if ($fieldlist[$field]=='height')          { $valuetoshow=$langs->trans("Height"); }
                if ($fieldlist[$field]=='unit')            { $valuetoshow=$langs->trans("MeasuringUnit"); }
                if ($fieldlist[$field]=='region_id' || $fieldlist[$field]=='country_id') { $showfield=0; }
                if ($fieldlist[$field]=='accountancy_code'){ $valuetoshow=$langs->trans("AccountancyCode"); }
                if ($fieldlist[$field]=='accountancy_code_sell'){ $valuetoshow=$langs->trans("AccountancyCodeSell"); $sortable=0; }
                if ($fieldlist[$field]=='accountancy_code_buy'){ $valuetoshow=$langs->trans("AccountancyCodeBuy"); $sortable=0; }
				if ($fieldlist[$field]=='fk_pcg_version')  { $valuetoshow=$langs->trans("Pcg_version"); }
                if ($fieldlist[$field]=='account_parent')  { $valuetoshow=$langs->trans("Accountsparent"); }
                if ($fieldlist[$field]=='pcg_type')        { $valuetoshow=$langs->trans("Pcg_type"); }
                if ($fieldlist[$field]=='pcg_subtype')     { $valuetoshow=$langs->trans("Pcg_subtype"); }
                if ($fieldlist[$field]=='sortorder')       { $valuetoshow=$langs->trans("SortOrder"); }
	            if ($fieldlist[$field]=='short_label')     { $valuetoshow=$langs->trans("ShortLabel"); }
            	if ($fieldlist[$field]=='type_template')   { $valuetoshow=$langs->trans("TypeOfTemplate"); }

                // Affiche nom du champ
                if ($showfield)
                {
                    print getTitleFieldOfList($valuetoshow,0,$_SERVER["PHP_SELF"],($sortable?$fieldlist[$field]:''),($page?'page='.$page.'&':'').'&id='.$id,"","align=".$align,$sortfield,$sortorder);
                }
            }
			// Favorite - Only activated on country dictionary
            if ($id == 4) print getTitleFieldOfList($langs->trans("Favorite"),0,$_SERVER["PHP_SELF"],"favorite",($page?'page='.$page.'&':'').'&id='.$id,"",'align="center"',$sortfield,$sortorder);

			print getTitleFieldOfList($langs->trans("Status"),0,$_SERVER["PHP_SELF"],"active",($page?'page='.$page.'&':'').'&id='.$id,"",'align="center"',$sortfield,$sortorder);
            print getTitleFieldOfList('');
            print getTitleFieldOfList('');
            print '</tr>';

            // Lines with values
            while ($i < $num)
            {
                $var = ! $var;

                $obj = $db->fetch_object($resql);
                //print_r($obj);
                print '<tr '.$bc[$var].' id="rowid-'.$obj->rowid.'">';
                if ($action == 'edit' && ($rowid == (! empty($obj->rowid)?$obj->rowid:$obj->code)))
                {
                    print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$id.'" method="POST">';
                    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
                    print '<input type="hidden" name="page" value="'.$page.'">';
                    print '<input type="hidden" name="rowid" value="'.$rowid.'">';

                    $tmpaction='edit';
                    $parameters=array('fieldlist'=>$fieldlist, 'tabname'=>$tabname[$id]);
                    $reshook=$hookmanager->executeHooks('editDictionaryFieldlist',$parameters,$obj, $tmpaction);    // Note that $action and $object may have been modified by some hooks
                    $error=$hookmanager->error; $errors=$hookmanager->errors;

                    if (empty($reshook)) fieldList($fieldlist,$obj,$tabname[$id],'edit');

                    print '<td colspan="3" align="right"><a name="'.(! empty($obj->rowid)?$obj->rowid:$obj->code).'">&nbsp;</a><input type="submit" class="button" name="actionmodify" value="'.$langs->trans("Modify").'">';
                    print '&nbsp;<input type="submit" class="button" name="actioncancel" value="'.$langs->trans("Cancel").'"></td>';
                }
                else
                {
	              	$tmpaction = 'view';
                    $parameters=array('var'=>$var, 'fieldlist'=>$fieldlist, 'tabname'=>$tabname[$id]);
                    $reshook=$hookmanager->executeHooks('viewDictionaryFieldlist',$parameters,$obj, $tmpaction);    // Note that $action and $object may have been modified by some hooks

                    $error=$hookmanager->error; $errors=$hookmanager->errors;

                    if (empty($reshook))
                    {
                        foreach ($fieldlist as $field => $value)
                        {
                            $showfield=1;
                        	$align="left";
                            $valuetoshow=$obj->$fieldlist[$field];
                            if ($value == 'type_template')
                            {
                                $valuetoshow = isset($elementList[$valuetoshow])?$elementList[$valuetoshow]:$valuetoshow;
                            }
                            if ($value == 'element')
                            {
                                $valuetoshow = isset($elementList[$valuetoshow])?$elementList[$valuetoshow]:$valuetoshow;
                            }
                            else if ($value == 'source')
                            {
                                $valuetoshow = isset($sourceList[$valuetoshow])?$sourceList[$valuetoshow]:$valuetoshow;
                            }
                            else if ($valuetoshow=='all') {
                                $valuetoshow=$langs->trans('All');
                            }
                            else if ($fieldlist[$field]=='country') {
                                if (empty($obj->country_code))
                                {
                                    $valuetoshow='-';
                                }
                                else
                                {
                                    $key=$langs->trans("Country".strtoupper($obj->country_code));
                                    $valuetoshow=($key != "Country".strtoupper($obj->country_code)?$obj->country_code." - ".$key:$obj->country);
                                }
                            }
                            else if ($fieldlist[$field]=='recuperableonly' || $fieldlist[$field]=='fdm' || $fieldlist[$field] == 'deductible') {
                                $valuetoshow=yn($valuetoshow);
                                $align="center";
                            }
                            else if ($fieldlist[$field]=='price' || preg_match('/^amount/i',$fieldlist[$field])) {
                                $valuetoshow=price($valuetoshow);
                            }
                            else if ($fieldlist[$field]=='libelle_facture') {
                                $langs->load("bills");
                                $key=$langs->trans("PaymentCondition".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "PaymentCondition".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                                $valuetoshow=nl2br($valuetoshow);
                            }
                            else if ($fieldlist[$field]=='label' && $tabname[$id]==MAIN_DB_PREFIX.'c_country') {
                                $key=$langs->trans("Country".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "Country".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='label' && $tabname[$id]==MAIN_DB_PREFIX.'c_availability') {
                                $langs->load("propal");
                                $key=$langs->trans("AvailabilityType".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "AvailabilityType".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_actioncomm') {
                                $key=$langs->trans("Action".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "Action".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if (! empty($obj->code_iso) && $fieldlist[$field]=='label' && $tabname[$id]==MAIN_DB_PREFIX.'c_currencies') {
                                $key=$langs->trans("Currency".strtoupper($obj->code_iso));
                                $valuetoshow=($obj->code_iso && $key != "Currency".strtoupper($obj->code_iso)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_typent') {
                                $key=$langs->trans(strtoupper($obj->code));
                                $valuetoshow=($key != strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_prospectlevel') {
                                $key=$langs->trans(strtoupper($obj->code));
                                $valuetoshow=($key != strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='label' && $tabname[$id]==MAIN_DB_PREFIX.'c_civility') {
                                $key=$langs->trans("Civility".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "Civility".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_type_contact') {
                            	$langs->load('agenda');
                                $key=$langs->trans("TypeContact_".$obj->element."_".$obj->source."_".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "TypeContact_".$obj->element."_".$obj->source."_".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_payment_term') {
                                $langs->load("bills");
                                $key=$langs->trans("PaymentConditionShort".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "PaymentConditionShort".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_paiement') {
                                $langs->load("bills");
                                $key=$langs->trans("PaymentType".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "PaymentType".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='label' && $tabname[$id]==MAIN_DB_PREFIX.'c_input_reason') {
                                $key=$langs->trans("DemandReasonType".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "DemandReasonType".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_input_method') {
                                $langs->load("orders");
                                $key=$langs->trans($obj->code);
                                $valuetoshow=($obj->code && $key != $obj->code)?$key:$obj->$fieldlist[$field];
                            }
                            else if ($fieldlist[$field]=='libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_shipment_mode') {
                                $langs->load("sendings");
                                $key=$langs->trans("SendingMethod".strtoupper($obj->code));
                                $valuetoshow=($obj->code && $key != "SendingMethod".strtoupper($obj->code)?$key:$obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field] == 'libelle' && $tabname[$id]==MAIN_DB_PREFIX.'c_paper_format')
                            {
                                $key = $langs->trans('PaperFormat'.strtoupper($obj->code));
                                $valuetoshow = ($obj->code && $key != 'PaperFormat'.strtoupper($obj->code) ? $key : $obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field] == 'label' && $tabname[$id] == MAIN_DB_PREFIX.'c_type_fees')
                            {
                                $langs->load('trips');
                                $key = $langs->trans(strtoupper($obj->code));
                                $valuetoshow = ($obj->code && $key != strtoupper($obj->code) ? $key : $obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='region_id' || $fieldlist[$field]=='country_id') {
                                $showfield=0;
                            }
                            else if ($fieldlist[$field]=='unicode') {
                            	$valuetoshow = $langs->getCurrencySymbol($obj->code,1);
                            }
                            else if ($fieldlist[$field]=='label' && $tabname[$_GET["id"]]==MAIN_DB_PREFIX.'c_units') {
	                            $langs->load("products");
	                            $valuetoshow=$langs->trans($obj->$fieldlist[$field]);
                            }
                            else if ($fieldlist[$field]=='short_label' && $tabname[$_GET["id"]]==MAIN_DB_PREFIX.'c_units') {
	                            $langs->load("products");
	                            $valuetoshow = $langs->trans($obj->$fieldlist[$field]);
                            }
                            else if (($fieldlist[$field] == 'unit') && ($tabname[$id] == MAIN_DB_PREFIX.'c_paper_format'))
                            {
                            	$key = $langs->trans('SizeUnit'.strtolower($obj->unit));
                                $valuetoshow = ($obj->code && $key != 'SizeUnit'.strtolower($obj->unit) ? $key : $obj->$fieldlist[$field]);
                            }

							else if ($fieldlist[$field]=='localtax1_type') {
							  if ($obj->localtax1 != 0)
							    $valuetoshow=$localtax_typeList[$valuetoshow];
							  else
							    $valuetoshow = '';
							  $align="center";
							}
							else if ($fieldlist[$field]=='localtax2_type') {
							 if ($obj->localtax2 != 0)
							    $valuetoshow=$localtax_typeList[$valuetoshow];
							  else
							    $valuetoshow = '';
							  $align="center";
							}
							else if ($fieldlist[$field]=='localtax1') {
                                $valuetoshow = price($valuetoshow, 0, $langs, 0, 0);
							  if ($obj->localtax1 == 0)
							    $valuetoshow = '';
							  $align="right";
							}
							else if ($fieldlist[$field]=='localtax2') {
                                $valuetoshow = price($valuetoshow, 0, $langs, 0, 0);
							  if ($obj->localtax2 == 0)
							    $valuetoshow = '';
							  $align="right";
							}
							else if (in_array($fieldlist[$field],array('taux','localtax1','localtax2')))
							{
                                $valuetoshow = price($valuetoshow, 0, $langs, 0, 0);
								$align="right";
							}
							else if (in_array($fieldlist[$field],array('recuperableonly')))
							{
								$align="center";
							}

							// Show value for field
							if ($showfield) print '<td align="'.$align.'">'.$valuetoshow.'</td>';
                        }
                    }

                    // Can an entry be erased or disabled ?
                    $iserasable=1;$isdisable=1;	// true by default

                    if (isset($obj->code))
                    {
                    	if (($obj->code == '0' || $obj->code == '' || preg_match('/unknown/i',$obj->code))) { $iserasable = 0; $isdisable = 0; }
                    	else if ($obj->code == 'RECEP') { $iserasable = 0; $isdisable = 0; }
                    	else if ($obj->code == 'EF0')   { $iserasable = 0; $isdisable = 0; }
                    }

                    if (isset($obj->type) && in_array($obj->type, array('system', 'systemauto'))) { $iserasable=0; }
                    if (in_array($obj->code, array('AC_OTH','AC_OTH_AUTO')) || in_array($obj->type, array('systemauto'))) { $isdisable=0; $isdisable = 0; }

                    $url = $_SERVER["PHP_SELF"].'?'.($page?'page='.$page.'&':'').'sortfield='.$sortfield.'&sortorder='.$sortorder.'&rowid='.(! empty($obj->rowid)?$obj->rowid:(! empty($obj->code)?$obj->code:'')).'&amp;code='.(! empty($obj->code)?urlencode($obj->code):'').'&amp;id='.$id.'&amp;';

					// Favorite
					// Only activated on country dictionary
                    if ($id == 4)
					{
						print '<td align="center" class="nowrap">';
						if ($iserasable) print '<a href="'.$url.'action='.$acts[$obj->favorite].'_favorite">'.$actl[$obj->favorite].'</a>';
						else print $langs->trans("AlwaysActive");
						print '</td>';
					}

                    // Active
                    print '<td align="center" class="nowrap">';
                    if ($isdisable) print '<a href="'.$url.'action='.$acts[$obj->active].'">'.$actl[$obj->active].'</a>';
                    else
                 	{
                 		if (in_array($obj->code, array('AC_OTH','AC_OTH_AUTO'))) print $langs->trans("AlwaysActive");
                 		else if (isset($obj->type) && in_array($obj->type, array('systemauto')) && empty($obj->active)) print $langs->trans("Deprecated");
                  		else if (isset($obj->type) && in_array($obj->type, array('system')) && ! empty($obj->active) && $obj->code != 'AC_OTH') print $langs->trans("UsedOnlyWithTypeOption");
                    	else print $langs->trans("AlwaysActive");
                    }
                    print "</td>";

                    // Modify link
                    if ($iserasable) print '<td align="center"><a href="'.$url.'action=edit#'.(! empty($obj->rowid)?$obj->rowid:(! empty($obj->code)?$obj->code:'')).'">'.img_edit().'</a></td>';
                    else print '<td>&nbsp;</td>';

                    // Delete link
                    if ($iserasable) print '<td align="center"><a href="'.$url.'action=delete">'.img_delete().'</a></td>';
                    else print '<td>&nbsp;</td>';

                    print "</tr>\n";
                }
                $i++;
            }
        }
    }
    else {
        dol_print_error($db);
    }

    print '</table>';

    print '</form>';
}
else
{
    /*
     * Show list of dictionary to show
     */

    $var=true;
    $lastlineisempty=false;
    print '<table class="noborder" width="100%">';
    print '<tr class="liste_titre">';
    //print '<td>'.$langs->trans("Module").'</td>';
    print '<td colspan="2">'.$langs->trans("Dictionary").'</td>';
    print '<td>'.$langs->trans("Table").'</td>';
    print '</tr>';

    $showemptyline='';
    foreach ($taborder as $i)
    {
        if (isset($tabname[$i]) && empty($tabcond[$i])) continue;

        if ($i)
        {
        	if ($showemptyline)
        	{
        		$var=!$var;
        		print '<tr '.$bc[$var].'><td width="30%">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        		$showemptyline=0;
        	}

            $var=!$var;
            $value=$tabname[$i];
            print '<tr '.$bc[$var].'><td width="50%">';
            if (! empty($tabcond[$i]))
            {
                print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$i.'">'.$langs->trans($tablib[$i]).'</a>';
            }
            else
            {
                print $langs->trans($tablib[$i]);
            }
            print '</td>';
            print '<td>';
            /*if (empty($tabcond[$i]))
             {
             print info_admin($langs->trans("DictionaryDisabledSinceNoModuleNeedIt"),1);
             }*/
            print '</td>';
            print '<td>'.$tabname[$i].'</td></tr>';
            $lastlineisempty=false;
        }
        else
        {
            if (! $lastlineisempty)
            {
                $showemptyline=1;
                $lastlineisempty=true;
            }
        }
    }
    print '</table>';
}

print '<br>';


llxFooter();
$db->close();


/**
 *	Show fields in insert/edit mode
 *
 * 	@param		array	$fieldlist		Array of fields
 * 	@param		Object	$obj			If we show a particular record, obj is filled with record fields
 *  @param		string	$tabname		Name of SQL table
 *  @param		string	$context		'add'=Output field for the "add form", 'edit'=Output field for the "edit form", 'hide'=Output field for the "add form" but we dont want it to be rendered
 *	@return		void
 */
function fieldList($fieldlist, $obj='', $tabname='', $context='')
{
	global $conf,$langs,$db;
	global $form;
	global $region_id;
	global $elementList,$sourceList,$localtax_typeList;
	global $bc;

	$formadmin = new FormAdmin($db);
	$formcompany = new FormCompany($db);

	foreach ($fieldlist as $field => $value)
	{
		if ($fieldlist[$field] == 'country')
		{
			if (in_array('region_id',$fieldlist))
			{
				print '<td>';
				//print join(',',$fieldlist);
				print '</td>';
				continue;
			}	// For state page, we do not show the country input (we link to region, not country)
			print '<td>';
			$fieldname='country';
			print $form->select_country((! empty($obj->country_code)?$obj->country_code:(! empty($obj->country)?$obj->country:'')), $fieldname, '', 28);
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'country_id')
		{
			if (! in_array('country',$fieldlist))	// If there is already a field country, we don't show country_id (avoid duplicate)
			{
				$country_id = (! empty($obj->$fieldlist[$field]) ? $obj->$fieldlist[$field] : 0);
				print '<td>';
				print '<input type="hidden" name="'.$fieldlist[$field].'" value="'.$country_id.'">';
				print '</td>';
			}
		}
		elseif ($fieldlist[$field] == 'region')
		{
			print '<td>';
			$formcompany->select_region($region_id,'region');
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'region_id')
		{
			$region_id = (! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:0);
			print '<td>';
			print '<input type="hidden" name="'.$fieldlist[$field].'" value="'.$region_id.'">';
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'lang')
		{
			print '<td>';
			print $formadmin->select_language($conf->global->MAIN_LANG_DEFAULT,'lang');
			print '</td>';
		}
		// Le type de template
		elseif ($fieldlist[$field] == 'type_template')
		{
			print '<td>';
			print $form->selectarray('type_template', $elementList,(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''));
			print '</td>';
		}
		// Le type de l'element (pour les type de contact)
		elseif ($fieldlist[$field] == 'element')
		{
			print '<td>';
			print $form->selectarray('element', $elementList,(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''));
			print '</td>';
		}
		// La source de l'element (pour les type de contact)
		elseif ($fieldlist[$field] == 'source')
		{
			print '<td>';
			print $form->selectarray('source', $sourceList,(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''));
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'type' && $tabname == MAIN_DB_PREFIX."c_actioncomm")
		{
			print '<td>';
			print 'user<input type="hidden" name="type" value="user">';
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'recuperableonly' || $fieldlist[$field] == 'fdm' || $fieldlist[$field] == 'deductible') {
			print '<td>';
			print $form->selectyesno($fieldlist[$field],(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''),1);
			print '</td>';
		}
		elseif (in_array($fieldlist[$field],array('nbjour','decalage','taux','localtax1','localtax2'))) {
			$align="left";
			if (in_array($fieldlist[$field],array('taux','localtax1','localtax2'))) $align="right";	// Fields aligned on right
			print '<td align="'.$align.'">';
			print '<input type="text" class="flat" value="'.(isset($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" size="3" name="'.$fieldlist[$field].'">';
			print '</td>';
		}
		elseif (in_array($fieldlist[$field], array('libelle_facture'))) {
			print '<td><textarea cols="30" rows="'.ROWS_2.'" class="flat" name="'.$fieldlist[$field].'">'.(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'</textarea></td>';
		}
		elseif (in_array($fieldlist[$field], array('content')))
		{
			if ($tabname == MAIN_DB_PREFIX.'c_email_templates')
			{
				print '<td colspan="4"></td></tr><tr class="pair nohover"><td colspan="5">';		// To create an artificial CR for the current tr we are on
			}
			else print '<td>';
			if ($context != 'hide')
			{
				//print '<textarea cols="3" rows="'.ROWS_2.'" class="flat" name="'.$fieldlist[$field].'">'.(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'</textarea>';
				$doleditor = new DolEditor($fieldlist[$field], (! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''), '', 140, 'dolibarr_mailings', 'In', 0, false, true, ROWS_5, '90%');
				print $doleditor->Create(1);
			}
			else print '&nbsp;';
			print '</td>';
		}
		elseif ($fieldlist[$field] == 'price' || preg_match('/^amount/i',$fieldlist[$field])) {
			print '<td><input type="text" class="flat" value="'.price((! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'')).'" size="8" name="'.$fieldlist[$field].'"></td>';
		}
		elseif ($fieldlist[$field] == 'code' && isset($obj->$fieldlist[$field])) {
			print '<td><input type="text" class="flat" value="'.(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" size="10" name="'.$fieldlist[$field].'"></td>';
		}
		elseif ($fieldlist[$field]=='unit') {
			print '<td>';
			$units = array(
					'mm' => $langs->trans('SizeUnitmm'),
					'cm' => $langs->trans('SizeUnitcm'),
					'point' => $langs->trans('SizeUnitpoint'),
					'inch' => $langs->trans('SizeUnitinch')
			);
			print $form->selectarray('unit', $units, (! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''), 0, 0, 0);
			print '</td>';
		}
		// Le type de taxe locale
		elseif ($fieldlist[$field] == 'localtax1_type' || $fieldlist[$field] == 'localtax2_type')
		{
			print '<td align="center">';
			print $form->selectarray($fieldlist[$field], $localtax_typeList, (! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''));
			print '</td>';
		}
		else
		{
			print '<td>';
			$size='';
			if ($fieldlist[$field]=='code') $size='size="8" ';
			if ($fieldlist[$field]=='position') $size='size="4" ';
			if ($fieldlist[$field]=='libelle') $size='size="32" ';
			if ($fieldlist[$field]=='tracking') $size='size="92" ';
			if ($fieldlist[$field]=='accountancy_code') $size='size="10" ';
			if ($fieldlist[$field]=='accountancy_code_sell') $size='size="10" ';
			if ($fieldlist[$field]=='accountancy_code_buy') $size='size="10" ';
			if ($fieldlist[$field]=='sortorder') $size='size="2" ';
			print '<input type="text" '.$size.' class="flat" value="'.(isset($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" name="'.$fieldlist[$field].'">';
			print '</td>';
		}
	}
}

