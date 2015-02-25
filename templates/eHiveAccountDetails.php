<?php 
/*
	Copyright (C) 2012 Vernon Systems Limited
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if ($css_class == "") {
	echo '<div class="ehive-account-details">';
} else {
	echo '<div class="ehive-account-details '.$css_class.'">';
}
	if (!isset($eHiveApiErrorMessage)) {
		echo '<div class="ehive-item">';
		if (isset($options['hide_page_title'])) {
			echo '<h1>'.$account->publicProfileName.'</h1>';
		} else {
			echo '<h2>'.$account->publicProfileName.'</h2>';
		} 
	
		$galleryInlineStyleEnabled = false;
		$imageInlineStyleEnabled = false;
		$galleryInlineStyle = '';
		$imageInlineStyle = '';
		
		if (isset($options['gallery_background_colour_enabled']) && $options['gallery_background_colour_enabled'] == 'on') {
			$galleryInlineStyle .= "background-color:{$options['gallery_background_colour']}; ";
			$galleryInlineStyleEnabled = true;
		}
		if (isset($options['gallery_border_colour_enabled']) && $options['gallery_border_colour_enabled'] == 'on' && $options['gallery_border_width'] > 0) {
			$galleryInlineStyle .= "border-style:solid; border-color:{$options['gallery_border_colour']}; ";
			$galleryInlineStyle .= "border-width:{$options['gallery_border_width']}px; *margin:-{$options['gallery_border_width']}px; ";
			$galleryInlineStyleEnabled = true;
		}
		if (isset($options['image_background_colour_enabled']) && $options['image_background_colour_enabled'] == 'on') {
			$imageInlineStyle .= "background:{$options['image_background_colour']}; ";
			$imageInlineStyleEnabled = true;
		}
		if (isset($options['image_padding_enabled']) && $options['image_padding_enabled'] == 'on') {
			$imageInlineStyle .= "padding:{$options['image_padding']}px; ";
			$imageInlineStyleEnabled = true;
		}
		if (isset($options['image_border_colour_enabled']) && $options['image_border_colour_enabled'] && $options['image_border_width'] > 0) {
			$imageInlineStyle .= "border-style:solid; border-color:{$options['image_border_colour']}; ";
			$imageInlineStyle .= "border-width:{$options['image_border_width']}px; ";
			$imageInlineStyleEnabled = true;
		}
		
		if($galleryInlineStyleEnabled) {
			$galleryInlineStyle = " style='$galleryInlineStyle'";
		}
		if($imageInlineStyleEnabled) {
			$imageInlineStyle = " style='$imageInlineStyle'";
		}
		
		$imageMediaSet = $account->getMediaSetByIdentifier('image');
		echo '<div class="ehive-item-image-wrap">';
		if (isset($imageMediaSet)){
		
			$numberOfImages = count($imageMediaSet->mediaRows);
			
			if ( $numberOfImages == 1 ) {
				
				$mediaRow = $imageMediaSet->mediaRows[0];
					
				$largeImageMedia = $mediaRow->getMediaByIdentifier('image_l');
				$smallImageMedia = $mediaRow->getMediaByIdentifier('image_s');				
	
				$imageWidth = $smallImageMedia->getMediaAttribute('width');
				$imageHeight = $smallImageMedia->getMediaAttribute('height');
				
				echo "<div class='ehive-account-single-image' $galleryInlineStyle>";
						echo '<a href="'.$largeImageMedia->getMediaAttribute('url').'" rel="prettyPhoto" target="_blank"><img src="'.$smallImageMedia->getMediaAttribute('url').'" alt="Account profile image" width="'.$smallImageMedia->getMediaAttribute('width').'px" height="'.$smallImageMedia->getMediaAttribute('height').'px" '.$imageInlineStyle.'/></a>';
					echo '<a href="'.$largeImageMedia->getMediaAttribute('url').'" class="zoom" rel="prettyPhoto" title="'.$largeImageMedia->getMediaAttribute('title').'"></a>';
				echo '</div>';
			
			} else if ( $numberOfImages > 1 ) {
				
				echo "<div class='ehive-account-multiple-images' $galleryInlineStyle>";
					echo '<div class="widget_style">';
						echo '<ul>';
						
							foreach ($imageMediaSet->mediaRows as $mediaRow) {
								$largeImageMedia = $mediaRow->getMediaByIdentifier('image_l');
								$smallImageMedia = $mediaRow->getMediaByIdentifier('image_s');								
								echo '<li>';
									echo '<a href="'.$largeImageMedia->getMediaAttribute('url').'" rel="prettyPhoto"><img src="'.$smallImageMedia->getMediaAttribute('url').'" alt="image zoom" width="'.$smallImageMedia->getMediaAttribute('width').'px" height="'.$smallImageMedia->getMediaAttribute('height').'px" '.$imageInlineStyle.'/></a>';
								echo '</li>';				
							}				
						echo '</ul>';
					echo '</div>';		
					echo '<div class="navigation">';
						echo '<a href="#" class="previous"></a>';					
						$mediaRow = $imageMediaSet->mediaRows[0];					
						$largeImageMedia = $mediaRow->getMediaByIdentifier('image_l');	
						$smallImageMedia = $mediaRow->getMediaByIdentifier('image_s');
						echo '<a href="'.$largeImageMedia->getMediaAttribute('url').'" class="zoom" rel="prettyPhoto" title="'.$smallImageMedia->getMediaAttribute('title').'"></a>';
						echo '<a href="#" class="next"></a>';
					echo '</div>';
				echo '</div>';
			}		
		}
		echo '</div>';
		?>
		<div class="ehive-item-metadata-wrap">
		
			<p class="ehive-field ehive-identifier-about-collection"><?php echo nl2br($account->aboutCollection) ?></p>
	
			<?php if ($account->postalAddress || $account->phoneNumber || $account->facsimile || $account->website || $account->emailAddress  ) {?>
			<div class="ehive-field ehive-identifier-contact-details">
				<span class="ehive-field-label">Contact Details</span>	
				<ul>	
				<?php if ($account->postalAddress) { ?>
					<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/postal.png" width="21" height="15" alt="Postal address" title="Postal address"/>
						<?php echo $account->postalAddress ?>
					</li>
				<?php }?>
		
				<?php if ($account->phoneNumber) { ?>
					<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/telephone.png" width="21" height="15" alt="Telephone number" title="Telephone number"/>
			        	<?php echo $account->phoneNumber ?>
		        	</li>
		        <?php }?>
		        
		        <?php if ($account->facsimile) {?>
		        	<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/fax.png" width="21" height="15" alt="Fax number" title="Fax number"/>
			        	<?php echo $account->facsimile ?>
		        	</li>
		        <?php }?>
		        
		        <?php if ($account->website) {?>
		        	<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/website.png" width="21" height="16" alt="Web address" title="Web address"/>
			        	<a href='<?php echo $account->website ?>' target='_blank'><?php echo $account->website ?></a>
		        	</li>
		        <?php }?>
		        
		        <?php if ($account->emailAddress) {?>
		        	<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/email.png" width="21" height="15" alt="eMail address" title="eMail address"/>
			        	<a href='mailto:<?php echo $account->emailAddress?>' target='_blank'><?php echo $account->emailAddress ?></a>
		        	</li>
		        <?php }?>     
		        </ul>   
	    	</div>
			<?php }?>
		
			<?php if ($account->physicalAddress || $account->hoursOfOperation || $account->admissionCharges ) {?>
			<div class="ehive-field ehive-identifier-visitor-information">
				<span class="ehive-field-label">Visitor Information</span>			
				<ul>
				<?php if ($account->physicalAddress) { ?>
					<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/physical.png" width="21" height="15" alt="Physical address" title="Physical address"/>
						<?php echo $account->physicalAddress ?>
					</li>
				<?php }?>
			
				<?php if ($account->hoursOfOperation) { ?>
					<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/hours.png" width="21" height="15" alt="Hours of operation" title="Hours of operation"/>
						<?php echo $account->hoursOfOperation ?>
					</li>
				<?php }?>
		
				<?php if ($account->admissionCharges) { ?>
					<li>
						<img src="<?php echo EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR?>images/charges.png" width="21" height="15" alt="Admission charges" title="Admission charges"/>
						<?php echo $account->admissionCharges ?>
					</li>
				<?php }?>
				</ul>
			</div>
			<?php }?>
		
			<?php if ($account->wheelChairAccessFacility || $account->cafeFacility || $account->referenceLibraryFacility ||
					  $account->parkingFacility || $account->shopFacility || $account->functionSpaceFacility ||
					  $account->guidedTourFacility || $account->publicProgrammesFacility || $account->membershipClubFacility ||
					  $account->toiletFacility || $account->otherFacility) {?>
			<div class="ehive-field ehive-identifier-facilities">
				<span class="ehive-field-label">Facilities</span>
				<ul>
				<?php
				if ($account->wheelChairAccessFacility) echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/wheelchairaccess.png" alt="Wheelchair Access" title="Wheelchair Access"/></li>';
				if ($account->cafeFacility)             echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/cafe.png" alt="Cafe" title="Cafe"/></li>';
				if ($account->referenceLibraryFacility) echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/referencelibrary.png" alt="Reference Library" title="Reference Library"/></li>';
				if ($account->parkingFacility)          echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/parking.png" alt="Parking" title="Parking"/></li>';
				if ($account->shopFacility)             echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/shop.png" alt="Shop" title="Shop"/></li>';
				if ($account->functionSpaceFacility)    echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/functionspaces.png" alt="Function Space" title="Function Space"/></li>';
				if ($account->guidedTourFacility)       echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/guidedtour.png" alt="Guided Tours" title="Guided Tours"/></li>';
				if ($account->publicProgrammesFacility) echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/publicprogrammes.png" alt="Public Programmes" title="Public Programmes"/></li>';
				if ($account->membershipClubFacility)   echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/membershipclub.png" alt="Membership Club" title="Membership Club"/></li>';
				if ($account->toiletFacility)           echo '<li><img src="'.EHIVE_ACCOUNT_DETAILS_PLUGIN_DIR.'images/toilets.png" alt="Toilets" title="Toilets"/></li>';	
				if ($account->otherFacility)            echo '<li class="ehive-identifier-other-facility">'.$account->otherFacility.'</li>';	
				?>
				</ul>
			</div>
			<?php }?>	
		
			<?php if ($account->latitude != 0  && $account->longitude != 0 && $account->zoomLevel != 0) { ?>
			<div class="ehive-field ehive-identifier-location">
				<span class="ehive-field-label">Location</span>
				<div class="google" id="googleMap" style="height: 400px; width: 480px">
					<div style="display:none">
						<span id="latitude" ><?php echo $account->latitude ?></span>
						<span id="longitude"><?php echo $account->longitude ?></span>			
						<span id="zoomLevel"><?php echo $account->zoomLevel ?></span>
					</div>
				</div>
			</div>
			<?php } ?>	
		</div>	
		
	</div>
	<?php } else { ?>
	
		<p class='ehive-error-message ehive-account-details-error'><?php echo $eHiveApiErrorMessage; ?></p>
		
	<?php } ?>
</div>