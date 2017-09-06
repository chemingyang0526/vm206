<!--
/*
    htvcenter Enterprise developed by htvcenter Enterprise GmbH.

    All source code and content (c) Copyright 2014, htvcenter Enterprise GmbH unless specifically noted otherwise.

    This source code is released under the htvcenter Enterprise Server and Client License, unless otherwise agreed with htvcenter Enterprise GmbH.
    The latest version of this license can be found here: http://htvcenter-enterprise.com/license

    By using this software, you acknowledge having read this license and agree to be bound thereby.

                http://htvcenter-enterprise.com

    Copyright 2014, htvcenter Enterprise GmbH <info@htvcenter-enterprise.com>
*/

{?}

-->
<h2>{label}</h2>

<div id="details">

<form action="{thisfile}">
{form}
{name}

<!--
{ip_mgmt_id}
{ip_mgmt_user_id}
{ip_mgmt_appliance_id}
{ip_mgmt_state}
{ip_mgmt_address}
//-->

<div style="float:left; width: 250px; margin: 20px 0 0 0;">
{ip_mgmt_name}
{ip_mgmt_network_1}
{ip_mgmt_network_2}
{ip_mgmt_subnet}
{ip_mgmt_broadcast}
{ip_mgmt_gateway}
{ip_mgmt_dns1}
{ip_mgmt_dns2}
{ip_mgmt_domain}
{ip_mgmt_vlan_id}
{ip_mgmt_comment}
</div>

<div style="float:left; width: 650px; margin: 0 0 0 50px;">
{table}
</div>

<div style="clear:both" class="floatbreaker">&#160;</div>
</form>

</div>
