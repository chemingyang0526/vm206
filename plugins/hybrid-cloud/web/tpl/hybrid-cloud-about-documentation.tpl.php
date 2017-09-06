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
//-->
<h2>{label}</h2>

<div class="row">
	<div class="tab-base span7">

 	<ul class="nav nav-tabs">
 									<li class="active">
										<a href="#demo-lft-tab-1" data-toggle="tab">{introduction_title}</a>
									</li>
									<li>
										<a href="#demo-lft-tab-2" data-toggle="tab">Configure</a>
									</li>
									<li>
										<a href="#demo-lft-tab-3" data-toggle="tab">Manage & Automate</a>
									</li>

									

									<li>
										<a href="#demo-lft-tab-4" data-toggle="tab">Definitions</a>
									</li>

									<li>
										<a href="#demo-lft-tab-5" data-toggle="tab">Automation</a>
									</li>
									<li>
										<a href="#demo-lft-tab-6" data-toggle="tab">Plugin Info</a>
									</li>

								</ul>
		
	<div class="tab-content">
 		
 		<div class="tab-pane fade active in" id="demo-lft-tab-1">
		<h3>Introduction</h3>
		<p>The hybrid cloud plugin integrates with Amazon EC2 and compatible substitutions such as Eucalyptus Cloud.<br>
				Additionally it provides a seamless migration path 'from' and 'to' public or private cloud providers.</p>
		<hr>
		</div>
		
		<div class="tab-pane fade" id="demo-lft-tab-2">
		<h3>Configure hybrid cloud account</h3>
		<p>Create a new hybrid cloud account configuration using the 'Actions' menu item. The following information is required:
				</p><ul>
					<li>Hybrid-cloud account name</li>
					<li>Cloud type</li>
					<li>Access key ID</li>
					<li>Secret access key</li>
					<li>Description</li>
				</ul>
				<hr>
		</div>

		<div class="tab-pane fade" id="demo-lft-tab-3">
		<h3>Manage and automate public and private clouds</h3>
		<h4>AMIs</h4>
				<p>The AMI action allows to easily add (and remove) htvcenter Image Objects for a specific public or private AMI.<br>
				A list of e.g. public available Ubuntu AMIs for each Amazon EC2 Region is available at <a target="BLANK" href="http://uec-images.ubuntu.com/">http://uec-images.ubuntu.com/</a>
				</p>
				<h5>Import - import AMIs from a public or private Cloud</h5>
				<p>To import a cloud server (the AMI of an active EC2 instance) follow the steps below:
				</p><ol>
					<li>Select a hybrid cloud account to use for import</li>
					<li>Select an active public cloud instance running the AMI to import</li>
					<li>Select an (empty) htvcenter server image (of type NFS or LVM-NFS)</li>
					<li>Provide the private SSH key file (Keypair) of the instance to import</li>
				</ol>
				This will automatically import the AMI from the selected public cloud instance into the (previously created) empty server image in htvcenter.<p></p>
				<p>The imported AMI now can be used through 'network-deployment' in htvcenter so e.g. it can now also run on a physical system or on any other virtualization type.</p>

				<h5>Export - export htvcenter Images to a public or private cloud</h5>
				<p>To export an htvcenter image to a public cloud provider as an AMI follow the steps below:
				</p><ol>
					<li>Select a hybrid cloud account to use for the export</li>
					<li>Select the image (of type NFS or LVM-NFS) to turn into an AMI for the export</li>
					<li>Provide a name for the AMI and additional parameters such as size, architecture, the public and private key file plus the EC2 user ID (AWS Account ID)</li>
				</ol>
				This will automatically export the selected htvcenter image to the public cloud provider.	It will be available as new AMI as soon as the transfer procedure is finished.<p></p>
				<hr>

		
		</div>
		<div class="tab-pane fade" id="demo-lft-tab-4">
			<h4>Instances</h4>
				<p>In a simple way new instances can be launched in a specific public or private cloud region via the INSTANCES action.
				Additional to the common instance configuration parameters such as instance type, the security group and keypair a custom configuration
				script (URL) can be attached to the instance.
				<br><br>
				<i>Hint: Custom configuration scripts to automatically pre-configure the instance can be easily uploaded to S3 with the S3 actions file-manager.</i>

				</p>
				<hr>

				<h4>Groups</h4>
				<p>Security Groups and custom firewall permissions are managed by the GROUPS action. Security Groups are attached to Instances to automatically configure the in- and outbound IP traffic.

				</p>
				<hr>

				<h4>Keypairs</h4>
				<p>Keypairs are managed by the KEYPAIR action. Same as Security Groups a Keypair is attached to Instances to all SSH login to the running Instances via a specific private SSH key.

				</p>
				<hr>

				<h4>Volumes</h4>
				<p>The VOLUMES action allows to create and snapshot EBS Volumes which can then be used to attach (or detach) them as additional blockdevices (hardddisks) to the instance.

				</p>
				<hr>

				<h4>Snapshots</h4>
				<p>This action provides the capability to manage the snapshots of the EBS Volumes e.g. for backup or re-deployment.

				</p>
				<hr>

				<h4>S3</h4>
				<p>The S3 action provides a Web-File-Manager for S3. It manages buckets and file uploads.
				<br><br>
				<i>Hint: Create custom configuration scripts to automatically pre-configure specific Instances and upload them to a S3 bucket via the 'file-upload' action in the S3 file-manager.
				Then attach the S3 URL of a custom configuration scripts to an Instance during 'Add Instance'. During start the Instance then automatically fetches the custom script from the S3 URL and executes it.</i>

				</p>
				<hr>"

		</div>


		<div class="tab-pane fade" id="demo-lft-tab-5">
			<h3>Automated application deployment</h3>
		<h4>Automate application deployment for Hybrid-Cloud Instances with Puppet</h4>
				<p>
				The Hybrid-Cloud plugin is fully integrated with the htvcenter Puppet plugin.
				This allows to manage applianction deployment for Hybrid-Cloud Instances in the same as for the internal IT resources.
				Simply add custom Puppet recipes to
				<br>
				<br>
				<code>/usr/share/htvcenter/plugins/puppet/web/puppet/manifests/classes/</code>
				<br>
				<br>
				and group them into the the
				<br>
				<br>
				<code>/usr/share/htvcenter/plugins/puppet/web/puppet/manifests/groups/</code>
				<br>
				<br>
				Every recipe in the groups directory automatically appears as selectable Puppet deployment in the htvcenter UI.
				Edit your servers to assign them to the Hybrid-Cloud Instances.
				</p>
				<hr>
		<h3>Automated Montioring</h3>
		<h4>Automated service monitoring for Hybrid-Cloud Instances with Nagios</h4>
				<p>
				Similar to the Puppet integration the Hybrid-Cloud plugin fully integrated with the htvcenter Nagios plugin.
				This allows to manage service monitoring for Hybrid-Cloud Instances in the same as for the internal IT resources.


				</p>
				<hr>
		
		<h3>Automated Highavailability</h3>
		<h4>Automated Highavailability for Hybrid-Cloud Instances</h4>
				<p>
				htvcenter is cabable to provide highavailability on infra-structure level for Hybrid-Cloud Instances in a fully automated way.
				In case of an Instance failure or a failure of a complete availability-zone htvcenter's highavailbility plugin automatically triggers a seamless failover
				of the Instance to another availability-zone in the same region.
				</p>
				<hr>
		
		<h3>htvcenter Cloud Integration</h3>
		<h4>Automate and consolidate your public and private Cloud deployments with htvcenter Cloud </h4>
				<p>
				The Hybrid-Cloud Integration of htvcenter makes it easy to 're-sell' Public Cloud resources through htvcenter Private Cloud.
				This way htvcenter Cloud consolidates and fully automates internal deployments (within the own datacenter using own IT resources)
				and also external provisioning to different Public or other Private Cloud providers (using the Cloud Providers IT resources).
				Simply adding a Hybrid-Cloud virtualization product to the htvcenter Cloud product manager enables the this option.
				</p>
				<hr>
		</div>






		<div class="tab-pane fade" id="demo-lft-tab-6">
			<h3>Plugin Type</h3>
			Deployment
			
			<h3>Tested with</h3>
			<p>This plugin is tested with Debian, Ubuntu and CentOS.</p>
			
			<h3>Requirements</h3>
			<ul>
					<li>Latest EC2-API-Tools installed<br>Get them from <br><small><a href='http://aws.amazon.com/developertools/351' target='_BLANK'>http://aws.amazon.com/developertools/351</a></small></li>
					<li>Latest EC2-AMI-Tools installed<br>Get them from <br><small><a href='http://aws.amazon.com/developertools/368' target='_BLANK'>http://aws.amazon.com/developertools/368</a></small></li>
					</ul>
					<p><span class='pill orange'>IMPORTANT</span></p>
					<p>The ec2-* packages supplied by the linux distribution are usually outdated. Do NOT install the ec2 tools from there, but rather use the latest EC2 API/AMI tools directly from Amazon (see above).</p> 
					<hr>
					<p>For a custom installation location of those Tools please configure <code>htvcenter_CUSTOM_JAVA_HOME</code>, <code>htvcenter_CUSTOM_EC2_API_HOME</code> and <code>htvcenter_CUSTOM_EC2_AMI_HOME</code> in <code>htvcenter-plugin-hybrid-cloud.conf</code>.
					<hr>
					<p>For additional or custom Hybrid-Cloud regions please configure <code>htvcenter_PLUGIN_HYBRID_CLOUD_REGIONS</code> in <code>htvcenter-plugin-hybrid-cloud.conf</code></p>
					<hr>
					<p>For an Eucalyptus Cloud type please install the latest Euca-API/AMI tools respectively</p>
					<hr>
		</div>

		
		
	</div>
</div>
</div>


