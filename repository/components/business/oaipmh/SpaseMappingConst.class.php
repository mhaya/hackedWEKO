<?php

/**
 * Constant class that defines the choice candidate of Space mapping
 * Spaseマッピングの選択肢候補を定義した定数クラス
 * 
 * @package WEKO
 */

// --------------------------------------------------------------------
//
// $Id: SpaseMappingConst.class.php 68946 2016-06-16 09:47:19Z tatsuya_koyasu $
//
// Copyright (c) 2007 - 2008, National Institute of Informatics, 
// Research and Development Center for Scientific Information Resources
//
// This program is licensed under a Creative Commons BSD Licence
// http://creativecommons.org/licenses/BSD/
//
// --------------------------------------------------------------------

/**
 * Constant class that defines the choice candidate of Space mapping
 * Spaseマッピングの選択肢候補を定義した定数クラス
 * 
 * @package WEKO
 * @copyright (c) 2007, National Institute of Informatics, Research and Development Center for Scientific Information Resources
 * @license http://creativecommons.org/licenses/BSD/ This program is licensed under the BSD Licence
 * @access public
 */
class SpaseMappingConst
{
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceID)
     *
     * @var string
     */
    const CATALOG_RESOURCEID = "Catalog.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_RESOURCENAME = "Catalog.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_RELEASEDATE = "Catalog.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Description)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_DESCRIPTION = "Catalog.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Acknowledgement)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Acknowledgement)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_ACKNOWLEDGEMENT = "Catalog.ResourceHeader.Acknowledgement";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_CONTACT_PERSONID = "Catalog.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_CONTACT_ROLE = "Catalog.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_INFORMATIONURL_URL = "Catalog.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Catalog.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const CATALOG_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Catalog.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.RepositoryID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.RepositoryID)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_REPOSITORYID = "Catalog.AccessInformation.RepositoryID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.Availability)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.Availability)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_AVAILABILITY = "Catalog.AccessInformation.Availability";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.AccessRights)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.AccessRights)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_ACCESSRIGHTS = "Catalog.AccessInformation.AccessRights";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.AccessURL.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.AccessURL.Name)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_ACCESSURL_NAME = "Catalog.AccessInformation.AccessURL.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.AccessURL.URL)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_ACCESSURL_URL = "Catalog.AccessInformation.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.AccessURL.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.AccessURL.Description)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_ACCESSURL_DESCRIPTION = "Catalog.AccessInformation.AccessURL.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.Format)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.Format)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_FORMAT = "Catalog.AccessInformation.Format";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.AccessInformation.DataExtent.Quantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.AccessInformation.DataExtent.Quantity)
     *
     * @var string
     */
    const CATALOG_ACCESSINFORMATION_DATAEXTENT_QUANTITY = "Catalog.AccessInformation.DataExtent.Quantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.InstrumentID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.InstrumentID)
     *
     * @var string
     */
    const CATALOG_INSTRUMENTID = "Catalog.InstrumentID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.PhenomenonType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.PhenomenonType)
     *
     * @var string
     */
    const CATALOG_PHENOMENONTYPE = "Catalog.PhenomenonType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.TimeSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.TimeSpan.StartDate)
     *
     * @var string
     */
    const CATALOG_TIMESPAN_STARTDATE = "Catalog.TimeSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.TimeSpan.StopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.TimeSpan.StopDate)
     *
     * @var string
     */
    const CATALOG_TIMESPAN_STOPDATE = "Catalog.TimeSpan.StopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.TimeSpan.RelativeStopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.TimeSpan.RelativeStopDate)
     *
     * @var string
     */
    const CATALOG_TIMESPAN_RELATIVESTOPDATE = "Catalog.TimeSpan.RelativeStopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Keyword)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Keyword)
     *
     * @var string
     */
    const CATALOG_KEYWORD = "Catalog.Keyword";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Name)
     *
     * @var string
     */
    const CATALOG_PARAMETER_NAME = "Catalog.Parameter.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Description)
     *
     * @var string
     */
    const CATALOG_PARAMETER_DESCRIPTION = "Catalog.Parameter.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.CoordinateSystem.CoordinateRepresentation)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.CoordinateSystem.CoordinateRepresentation)
     *
     * @var string
     */
    const CATALOG_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION = "Catalog.Parameter.CoordinateSystem.CoordinateRepresentation";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.CoordinateSystem.CoordinateSystemName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.CoordinateSystem.CoordinateSystemName)
     *
     * @var string
     */
    const CATALOG_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME = "Catalog.Parameter.CoordinateSystem.CoordinateSystemName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Structure.Size)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Structure.Size)
     *
     * @var string
     */
    const CATALOG_PARAMETER_STRUCTURE_SIZE = "Catalog.Parameter.Structure.Size";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Structure.Element.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Structure.Element.Name)
     *
     * @var string
     */
    const CATALOG_PARAMETER_STRUCTURE_ELEMENT_NAME = "Catalog.Parameter.Structure.Element.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Structure.Element.Index)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Structure.Element.Index)
     *
     * @var string
     */
    const CATALOG_PARAMETER_STRUCTURE_ELEMENT_INDEX = "Catalog.Parameter.Structure.Element.Index";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FieldQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FieldQuantity)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FIELDQUANTITY = "Catalog.Parameter.Field.FieldQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FrequencyRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_LOW = "Catalog.Parameter.Field.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FrequencyRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_HIGH = "Catalog.Parameter.Field.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FrequencyRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_UNITS = "Catalog.Parameter.Field.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW = "Catalog.Parameter.Field.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Field.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Field.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH = "Catalog.Parameter.Field.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.ParticleType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.ParticleType)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_PARTICLETYPE = "Catalog.Parameter.Particle.ParticleType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.ParticleQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.ParticleQuantity)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_PARTICLEQUANTITY = "Catalog.Parameter.Particle.ParticleQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.EnergyRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_LOW = "Catalog.Parameter.Particle.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.EnergyRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_HIGH = "Catalog.Parameter.Particle.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.EnergyRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_UNITS = "Catalog.Parameter.Particle.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW = "Catalog.Parameter.Particle.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.EnergyRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH = "Catalog.Parameter.Particle.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.AzimuthalAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.AzimuthalAngleRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW = "Catalog.Parameter.Particle.AzimuthalAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.AzimuthalAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.AzimuthalAngleRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH = "Catalog.Parameter.Particle.AzimuthalAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.AzimuthalAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.AzimuthalAngleRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS = "Catalog.Parameter.Particle.AzimuthalAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW = "Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH = "Catalog.Parameter.Particle.AzimuthalAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.PolarAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.PolarAngleRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_LOW = "Catalog.Parameter.Particle.PolarAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.PolarAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.PolarAngleRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH = "Catalog.Parameter.Particle.PolarAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.PolarAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.PolarAngleRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS = "Catalog.Parameter.Particle.PolarAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.PolarAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.PolarAngleRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW = "Catalog.Parameter.Particle.PolarAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Particle.PolarAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Particle.PolarAngleRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH = "Catalog.Parameter.Particle.PolarAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WaveType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WaveType)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVETYPE = "Catalog.Parameter.Wave.WaveType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WaveQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WaveQuantity)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVEQUANTITY = "Catalog.Parameter.Wave.WaveQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.EnergyRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_ENERGYRANGE_LOW = "Catalog.Parameter.Wave.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.EnergyRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_ENERGYRANGE_HIGH = "Catalog.Parameter.Wave.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.EnergyRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_ENERGYRANGE_UNITS = "Catalog.Parameter.Wave.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW = "Catalog.Parameter.Wave.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.EnergyRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH = "Catalog.Parameter.Wave.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.FrequencyRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_LOW = "Catalog.Parameter.Wave.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.FrequencyRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_HIGH = "Catalog.Parameter.Wave.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.FrequencyRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_UNITS = "Catalog.Parameter.Wave.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW = "Catalog.Parameter.Wave.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH = "Catalog.Parameter.Wave.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WavelengthRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WavelengthRange.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_LOW = "Catalog.Parameter.Wave.WavelengthRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WavelengthRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WavelengthRange.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH = "Catalog.Parameter.Wave.WavelengthRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WavelengthRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WavelengthRange.Units)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS = "Catalog.Parameter.Wave.WavelengthRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WavelengthRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WavelengthRange.Bin.Low)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW = "Catalog.Parameter.Wave.WavelengthRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Wave.WavelengthRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Wave.WavelengthRange.Bin.High)
     *
     * @var string
     */
    const CATALOG_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH = "Catalog.Parameter.Wave.WavelengthRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Mixed.MixedQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Mixed.MixedQuantity)
     *
     * @var string
     */
    const CATALOG_PARAMETER_MIXED_MIXEDQUANTITY = "Catalog.Parameter.Mixed.MixedQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Catalog.Parameter.Support.SupportQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Catalog.Parameter.Support.SupportQuantity)
     *
     * @var string
     */
    const CATALOG_PARAMETER_SUPPORT_SUPPORTQUANTITY = "Catalog.Parameter.Support.SupportQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceID)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEID = "DisplayData.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_RESOURCENAME = "DisplayData.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_RELEASEDATE = "DisplayData.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Description)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_DESCRIPTION = "DisplayData.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Acknowledgement)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Acknowledgement)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_ACKNOWLEDGEMENT = "DisplayData.ResourceHeader.Acknowledgement";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_CONTACT_PERSONID = "DisplayData.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_CONTACT_ROLE = "DisplayData.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_INFORMATIONURL_URL = "DisplayData.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "DisplayData.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const DISPLAYDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "DisplayData.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.RepositoryID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.RepositoryID)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_REPOSITORYID = "DisplayData.AccessInformation.RepositoryID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.Availability)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.Availability)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_AVAILABILITY = "DisplayData.AccessInformation.Availability";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.AccessRights)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.AccessRights)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_ACCESSRIGHTS = "DisplayData.AccessInformation.AccessRights";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.AccessURL.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.AccessURL.Name)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_NAME = "DisplayData.AccessInformation.AccessURL.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.AccessURL.URL)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_URL = "DisplayData.AccessInformation.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.AccessURL.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.AccessURL.Description)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_ACCESSURL_DESCRIPTION = "DisplayData.AccessInformation.AccessURL.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.Format)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.Format)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_FORMAT = "DisplayData.AccessInformation.Format";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.AccessInformation.DataExtent.Quantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.AccessInformation.DataExtent.Quantity)
     *
     * @var string
     */
    const DISPLAYDATA_ACCESSINFORMATION_DATAEXTENT_QUANTITY = "DisplayData.AccessInformation.DataExtent.Quantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.InstrumentID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.InstrumentID)
     *
     * @var string
     */
    const DISPLAYDATA_INSTRUMENTID = "DisplayData.InstrumentID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.MeasurementType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.MeasurementType)
     *
     * @var string
     */
    const DISPLAYDATA_MEASUREMENTTYPE = "DisplayData.MeasurementType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.TemporalDescription.TimeSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.TemporalDescription.TimeSpan.StartDate)
     *
     * @var string
     */
    const DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_STARTDATE = "DisplayData.TemporalDescription.TimeSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.TemporalDescription.TimeSpan.StopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.TemporalDescription.TimeSpan.StopDate)
     *
     * @var string
     */
    const DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_STOPDATE = "DisplayData.TemporalDescription.TimeSpan.StopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.TemporalDescription.TimeSpan.RelativeStopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.TemporalDescription.TimeSpan.RelativeStopDate)
     *
     * @var string
     */
    const DISPLAYDATA_TEMPORALDESCRIPTION_TIMESPAN_RELATIVESTOPDATE = "DisplayData.TemporalDescription.TimeSpan.RelativeStopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.ObservedRegion)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.ObservedRegion)
     *
     * @var string
     */
    const DISPLAYDATA_OBSERVEDREGION = "DisplayData.ObservedRegion";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Keyword)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Keyword)
     *
     * @var string
     */
    const DISPLAYDATA_KEYWORD = "DisplayData.Keyword";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Name)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_NAME = "DisplayData.Parameter.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Description)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_DESCRIPTION = "DisplayData.Parameter.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.CoordinateSystem.CoordinateRepresentation)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.CoordinateSystem.CoordinateRepresentation)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION = "DisplayData.Parameter.CoordinateSystem.CoordinateRepresentation";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.CoordinateSystem.CoordinateSystemName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.CoordinateSystem.CoordinateSystemName)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME = "DisplayData.Parameter.CoordinateSystem.CoordinateSystemName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Structure.Size)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Structure.Size)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_STRUCTURE_SIZE = "DisplayData.Parameter.Structure.Size";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Structure.Element.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Structure.Element.Name)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_STRUCTURE_ELEMENT_NAME = "DisplayData.Parameter.Structure.Element.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Structure.Element.Index)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Structure.Element.Index)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_STRUCTURE_ELEMENT_INDEX = "DisplayData.Parameter.Structure.Element.Index";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FieldQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FieldQuantity)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FIELDQUANTITY = "DisplayData.Parameter.Field.FieldQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FrequencyRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_LOW = "DisplayData.Parameter.Field.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FrequencyRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_HIGH = "DisplayData.Parameter.Field.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FrequencyRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_UNITS = "DisplayData.Parameter.Field.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW = "DisplayData.Parameter.Field.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Field.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Field.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH = "DisplayData.Parameter.Field.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.ParticleType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.ParticleType)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_PARTICLETYPE = "DisplayData.Parameter.Particle.ParticleType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.ParticleQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.ParticleQuantity)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_PARTICLEQUANTITY = "DisplayData.Parameter.Particle.ParticleQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.EnergyRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_LOW = "DisplayData.Parameter.Particle.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.EnergyRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_HIGH = "DisplayData.Parameter.Particle.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.EnergyRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_UNITS = "DisplayData.Parameter.Particle.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW = "DisplayData.Parameter.Particle.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.EnergyRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH = "DisplayData.Parameter.Particle.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.AzimuthalAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.AzimuthalAngleRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW = "DisplayData.Parameter.Particle.AzimuthalAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.AzimuthalAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.AzimuthalAngleRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH = "DisplayData.Parameter.Particle.AzimuthalAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.AzimuthalAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.AzimuthalAngleRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS = "DisplayData.Parameter.Particle.AzimuthalAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW = "DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH = "DisplayData.Parameter.Particle.AzimuthalAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.PolarAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.PolarAngleRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_LOW = "DisplayData.Parameter.Particle.PolarAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.PolarAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.PolarAngleRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH = "DisplayData.Parameter.Particle.PolarAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.PolarAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.PolarAngleRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS = "DisplayData.Parameter.Particle.PolarAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.PolarAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.PolarAngleRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW = "DisplayData.Parameter.Particle.PolarAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Particle.PolarAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Particle.PolarAngleRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH = "DisplayData.Parameter.Particle.PolarAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WaveType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WaveType)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVETYPE = "DisplayData.Parameter.Wave.WaveType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WaveQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WaveQuantity)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVEQUANTITY = "DisplayData.Parameter.Wave.WaveQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.EnergyRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_LOW = "DisplayData.Parameter.Wave.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.EnergyRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_HIGH = "DisplayData.Parameter.Wave.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.EnergyRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_UNITS = "DisplayData.Parameter.Wave.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW = "DisplayData.Parameter.Wave.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.EnergyRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH = "DisplayData.Parameter.Wave.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.FrequencyRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_LOW = "DisplayData.Parameter.Wave.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.FrequencyRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_HIGH = "DisplayData.Parameter.Wave.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.FrequencyRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_UNITS = "DisplayData.Parameter.Wave.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW = "DisplayData.Parameter.Wave.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH = "DisplayData.Parameter.Wave.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WavelengthRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WavelengthRange.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_LOW = "DisplayData.Parameter.Wave.WavelengthRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WavelengthRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WavelengthRange.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH = "DisplayData.Parameter.Wave.WavelengthRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WavelengthRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WavelengthRange.Units)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS = "DisplayData.Parameter.Wave.WavelengthRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WavelengthRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WavelengthRange.Bin.Low)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW = "DisplayData.Parameter.Wave.WavelengthRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Wave.WavelengthRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Wave.WavelengthRange.Bin.High)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH = "DisplayData.Parameter.Wave.WavelengthRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Mixed.MixedQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Mixed.MixedQuantity)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_MIXED_MIXEDQUANTITY = "DisplayData.Parameter.Mixed.MixedQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(DisplayData.Parameter.Support.SupportQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(DisplayData.Parameter.Support.SupportQuantity)
     *
     * @var string
     */
    const DISPLAYDATA_PARAMETER_SUPPORT_SUPPORTQUANTITY = "DisplayData.Parameter.Support.SupportQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceID)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEID = "NumericalData.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_RESOURCENAME = "NumericalData.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_RELEASEDATE = "NumericalData.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Description)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_DESCRIPTION = "NumericalData.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Acknowledgement)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Acknowledgement)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_ACKNOWLEDGEMENT = "NumericalData.ResourceHeader.Acknowledgement";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_CONTACT_PERSONID = "NumericalData.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_CONTACT_ROLE = "NumericalData.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_INFORMATIONURL_URL = "NumericalData.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "NumericalData.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const NUMERICALDATA_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "NumericalData.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.RepositoryID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.RepositoryID)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_REPOSITORYID = "NumericalData.AccessInformation.RepositoryID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.Availability)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.Availability)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_AVAILABILITY = "NumericalData.AccessInformation.Availability";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.AccessRights)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.AccessRights)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_ACCESSRIGHTS = "NumericalData.AccessInformation.AccessRights";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.AccessURL.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.AccessURL.Name)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_NAME = "NumericalData.AccessInformation.AccessURL.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.AccessURL.URL)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_URL = "NumericalData.AccessInformation.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.AccessURL.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.AccessURL.Description)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_ACCESSURL_DESCRIPTION = "NumericalData.AccessInformation.AccessURL.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.Format)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.Format)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_FORMAT = "NumericalData.AccessInformation.Format";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.AccessInformation.DataExtent.Quantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.AccessInformation.DataExtent.Quantity)
     *
     * @var string
     */
    const NUMERICALDATA_ACCESSINFORMATION_DATAEXTENT_QUANTITY = "NumericalData.AccessInformation.DataExtent.Quantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.InstrumentID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.InstrumentID)
     *
     * @var string
     */
    const NUMERICALDATA_INSTRUMENTID = "NumericalData.InstrumentID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.MeasurementType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.MeasurementType)
     *
     * @var string
     */
    const NUMERICALDATA_MEASUREMENTTYPE = "NumericalData.MeasurementType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.TemporalDescription.TimeSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.TemporalDescription.TimeSpan.StartDate)
     *
     * @var string
     */
    const NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_STARTDATE = "NumericalData.TemporalDescription.TimeSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.TemporalDescription.TimeSpan.StopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.TemporalDescription.TimeSpan.StopDate)
     *
     * @var string
     */
    const NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_STOPDATE = "NumericalData.TemporalDescription.TimeSpan.StopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.TemporalDescription.TimeSpan.RelativeStopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.TemporalDescription.TimeSpan.RelativeStopDate)
     *
     * @var string
     */
    const NUMERICALDATA_TEMPORALDESCRIPTION_TIMESPAN_RELATIVESTOPDATE = "NumericalData.TemporalDescription.TimeSpan.RelativeStopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.ObservedRegion)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.ObservedRegion)
     *
     * @var string
     */
    const NUMERICALDATA_OBSERVEDREGION = "NumericalData.ObservedRegion";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Keyword)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Keyword)
     *
     * @var string
     */
    const NUMERICALDATA_KEYWORD = "NumericalData.Keyword";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Name)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_NAME = "NumericalData.Parameter.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Description)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_DESCRIPTION = "NumericalData.Parameter.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.CoordinateSystem.CoordinateRepresentation)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.CoordinateSystem.CoordinateRepresentation)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_COORDINATESYSTEM_COORDINATEREPRESENTATION = "NumericalData.Parameter.CoordinateSystem.CoordinateRepresentation";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.CoordinateSystem.CoordinateSystemName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.CoordinateSystem.CoordinateSystemName)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_COORDINATESYSTEM_COORDINATESYSTEMNAME = "NumericalData.Parameter.CoordinateSystem.CoordinateSystemName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Structure.Size)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Structure.Size)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_STRUCTURE_SIZE = "NumericalData.Parameter.Structure.Size";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Structure.Element.Name)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Structure.Element.Name)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_STRUCTURE_ELEMENT_NAME = "NumericalData.Parameter.Structure.Element.Name";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Structure.Element.Index)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Structure.Element.Index)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_STRUCTURE_ELEMENT_INDEX = "NumericalData.Parameter.Structure.Element.Index";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FieldQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FieldQuantity)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FIELDQUANTITY = "NumericalData.Parameter.Field.FieldQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FrequencyRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_LOW = "NumericalData.Parameter.Field.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FrequencyRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_HIGH = "NumericalData.Parameter.Field.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FrequencyRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_UNITS = "NumericalData.Parameter.Field.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_LOW = "NumericalData.Parameter.Field.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Field.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Field.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_FIELD_FREQUENCYRANGE_BIN_HIGH = "NumericalData.Parameter.Field.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.ParticleType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.ParticleType)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_PARTICLETYPE = "NumericalData.Parameter.Particle.ParticleType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.ParticleQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.ParticleQuantity)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_PARTICLEQUANTITY = "NumericalData.Parameter.Particle.ParticleQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.EnergyRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_LOW = "NumericalData.Parameter.Particle.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.EnergyRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_HIGH = "NumericalData.Parameter.Particle.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.EnergyRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_UNITS = "NumericalData.Parameter.Particle.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_LOW = "NumericalData.Parameter.Particle.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.EnergyRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_ENERGYRANGE_BIN_HIGH = "NumericalData.Parameter.Particle.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.AzimuthalAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.AzimuthalAngleRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_LOW = "NumericalData.Parameter.Particle.AzimuthalAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.AzimuthalAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.AzimuthalAngleRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_HIGH = "NumericalData.Parameter.Particle.AzimuthalAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.AzimuthalAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.AzimuthalAngleRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_UNITS = "NumericalData.Parameter.Particle.AzimuthalAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_LOW = "NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_AZIMUTHALANGLERANGE_BIN_HIGH = "NumericalData.Parameter.Particle.AzimuthalAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.PolarAngleRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.PolarAngleRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_LOW = "NumericalData.Parameter.Particle.PolarAngleRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.PolarAngleRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.PolarAngleRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_HIGH = "NumericalData.Parameter.Particle.PolarAngleRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.PolarAngleRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.PolarAngleRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_UNITS = "NumericalData.Parameter.Particle.PolarAngleRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.PolarAngleRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.PolarAngleRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_LOW = "NumericalData.Parameter.Particle.PolarAngleRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Particle.PolarAngleRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Particle.PolarAngleRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_PARTICLE_POLARANGLERANGE_BIN_HIGH = "NumericalData.Parameter.Particle.PolarAngleRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WaveType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WaveType)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVETYPE = "NumericalData.Parameter.Wave.WaveType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WaveQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WaveQuantity)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVEQUANTITY = "NumericalData.Parameter.Wave.WaveQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.EnergyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.EnergyRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_LOW = "NumericalData.Parameter.Wave.EnergyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.EnergyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.EnergyRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_HIGH = "NumericalData.Parameter.Wave.EnergyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.EnergyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.EnergyRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_UNITS = "NumericalData.Parameter.Wave.EnergyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.EnergyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.EnergyRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_LOW = "NumericalData.Parameter.Wave.EnergyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.EnergyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.EnergyRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_ENERGYRANGE_BIN_HIGH = "NumericalData.Parameter.Wave.EnergyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.FrequencyRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.FrequencyRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_LOW = "NumericalData.Parameter.Wave.FrequencyRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.FrequencyRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.FrequencyRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_HIGH = "NumericalData.Parameter.Wave.FrequencyRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.FrequencyRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.FrequencyRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_UNITS = "NumericalData.Parameter.Wave.FrequencyRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.FrequencyRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.FrequencyRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_LOW = "NumericalData.Parameter.Wave.FrequencyRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.FrequencyRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.FrequencyRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_FREQUENCYRANGE_BIN_HIGH = "NumericalData.Parameter.Wave.FrequencyRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WavelengthRange.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WavelengthRange.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_LOW = "NumericalData.Parameter.Wave.WavelengthRange.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WavelengthRange.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WavelengthRange.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_HIGH = "NumericalData.Parameter.Wave.WavelengthRange.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WavelengthRange.Units)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WavelengthRange.Units)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_UNITS = "NumericalData.Parameter.Wave.WavelengthRange.Units";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WavelengthRange.Bin.Low)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WavelengthRange.Bin.Low)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_LOW = "NumericalData.Parameter.Wave.WavelengthRange.Bin.Low";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Wave.WavelengthRange.Bin.High)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Wave.WavelengthRange.Bin.High)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_WAVE_WAVELENGTHRANGE_BIN_HIGH = "NumericalData.Parameter.Wave.WavelengthRange.Bin.High";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Mixed.MixedQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Mixed.MixedQuantity)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_MIXED_MIXEDQUANTITY = "NumericalData.Parameter.Mixed.MixedQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(NumericalData.Parameter.Support.SupportQuantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(NumericalData.Parameter.Support.SupportQuantity)
     *
     * @var string
     */
    const NUMERICALDATA_PARAMETER_SUPPORT_SUPPORTQUANTITY = "NumericalData.Parameter.Support.SupportQuantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceID)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEID = "Document.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_RESOURCENAME = "Document.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_RELEASEDATE = "Document.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.Description)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_DESCRIPTION = "Document.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_CONTACT_PERSONID = "Document.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_CONTACT_ROLE = "Document.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_INFORMATIONURL_URL = "Document.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Document.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const DOCUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Document.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.AccessInformation.RepositoryID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.AccessInformation.RepositoryID)
     *
     * @var string
     */
    const DOCUMENT_ACCESSINFORMATION_REPOSITORYID = "Document.AccessInformation.RepositoryID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.AccessInformation.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.AccessInformation.AccessURL.URL)
     *
     * @var string
     */
    const DOCUMENT_ACCESSINFORMATION_ACCESSURL_URL = "Document.AccessInformation.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.AccessInformation.Format)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.AccessInformation.Format)
     *
     * @var string
     */
    const DOCUMENT_ACCESSINFORMATION_FORMAT = "Document.AccessInformation.Format";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.AccessInformation.DataExtent.Quantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.AccessInformation.DataExtent.Quantity)
     *
     * @var string
     */
    const DOCUMENT_ACCESSINFORMATION_DATAEXTENT_QUANTITY = "Document.AccessInformation.DataExtent.Quantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.DocumentType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.DocumentType)
     *
     * @var string
     */
    const DOCUMENT_DOCUMENTTYPE = "Document.DocumentType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Document.MIMEType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Document.MIMEType)
     *
     * @var string
     */
    const DOCUMENT_MIMETYPE = "Document.MIMEType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.ResourceID)
     *
     * @var string
     */
    const GRANULE_RESOURCEID = "Granule.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.ReleaseDate)
     *
     * @var string
     */
    const GRANULE_RELEASEDATE = "Granule.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.ParentID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.ParentID)
     *
     * @var string
     */
    const GRANULE_PARENTID = "Granule.ParentID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.StartDate)
     *
     * @var string
     */
    const GRANULE_STARTDATE = "Granule.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.StopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.StopDate)
     *
     * @var string
     */
    const GRANULE_STOPDATE = "Granule.StopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.Source.SourceType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.Source.SourceType)
     *
     * @var string
     */
    const GRANULE_SOURCE_SOURCETYPE = "Granule.Source.SourceType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.Source.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.Source.URL)
     *
     * @var string
     */
    const GRANULE_SOURCE_URL = "Granule.Source.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.Source.Checksum.HashValue)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.Source.Checksum.HashValue)
     *
     * @var string
     */
    const GRANULE_SOURCE_CHECKSUM_HASHVALUE = "Granule.Source.Checksum.HashValue";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.Source.Checksum.HashFunction)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.Source.Checksum.HashFunction)
     *
     * @var string
     */
    const GRANULE_SOURCE_CHECKSUM_HASHFUNCTION = "Granule.Source.Checksum.HashFunction";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Granule.Source.DataExtent.Quantity)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Granule.Source.DataExtent.Quantity)
     *
     * @var string
     */
    const GRANULE_SOURCE_DATAEXTENT_QUANTITY = "Granule.Source.DataExtent.Quantity";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceID)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEID = "Instrument.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_RESOURCENAME = "Instrument.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_RELEASEDATE = "Instrument.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.Description)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_DESCRIPTION = "Instrument.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_CONTACT_PERSONID = "Instrument.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_CONTACT_ROLE = "Instrument.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_INFORMATIONURL_URL = "Instrument.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Instrument.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const INSTRUMENT_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Instrument.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.InstrumentType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.InstrumentType)
     *
     * @var string
     */
    const INSTRUMENT_INSTRUMENTTYPE = "Instrument.InstrumentType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.InvestigationName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.InvestigationName)
     *
     * @var string
     */
    const INSTRUMENT_INVESTIGATIONNAME = "Instrument.InvestigationName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.OperatingSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.OperatingSpan.StartDate)
     *
     * @var string
     */
    const INSTRUMENT_OPERATINGSPAN_STARTDATE = "Instrument.OperatingSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Instrument.ObservatoryID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Instrument.ObservatoryID)
     *
     * @var string
     */
    const INSTRUMENT_OBSERVATORYID = "Instrument.ObservatoryID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceID)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEID = "Observatory.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_RESOURCENAME = "Observatory.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_RELEASEDATE = "Observatory.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.Description)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_DESCRIPTION = "Observatory.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_CONTACT_PERSONID = "Observatory.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_CONTACT_ROLE = "Observatory.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_INFORMATIONURL_URL = "Observatory.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Observatory.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const OBSERVATORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Observatory.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.Location.ObservatoryRegion)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.Location.ObservatoryRegion)
     *
     * @var string
     */
    const OBSERVATORY_LOCATION_OBSERVATORYREGION = "Observatory.Location.ObservatoryRegion";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Observatory.OperatingSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Observatory.OperatingSpan.StartDate)
     *
     * @var string
     */
    const OBSERVATORY_OPERATINGSPAN_STARTDATE = "Observatory.OperatingSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Person.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Person.ResourceID)
     *
     * @var string
     */
    const PERSON_RESOURCEID = "Person.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Person.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Person.ReleaseDate)
     *
     * @var string
     */
    const PERSON_RELEASEDATE = "Person.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Person.PersonName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Person.PersonName)
     *
     * @var string
     */
    const PERSON_PERSONNAME = "Person.PersonName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Person.OrganizationName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Person.OrganizationName)
     *
     * @var string
     */
    const PERSON_ORGANIZATIONNAME = "Person.OrganizationName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Person.Email)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Person.Email)
     *
     * @var string
     */
    const PERSON_EMAIL = "Person.Email";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceID)
     *
     * @var string
     */
    const REGISTRY_RESOURCEID = "Registry.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_RESOURCENAME = "Registry.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_RELEASEDATE = "Registry.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.Description)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_DESCRIPTION = "Registry.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_CONTACT_PERSONID = "Registry.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_CONTACT_ROLE = "Registry.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_INFORMATIONURL_URL = "Registry.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Registry.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const REGISTRY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Registry.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Registry.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Registry.AccessURL.URL)
     *
     * @var string
     */
    const REGISTRY_ACCESSURL_URL = "Registry.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceID)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEID = "Repository.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_RESOURCENAME = "Repository.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_RELEASEDATE = "Repository.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.Description)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_DESCRIPTION = "Repository.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_CONTACT_PERSONID = "Repository.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_CONTACT_ROLE = "Repository.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_INFORMATIONURL_URL = "Repository.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Repository.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const REPOSITORY_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Repository.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Repository.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Repository.AccessURL.URL)
     *
     * @var string
     */
    const REPOSITORY_ACCESSURL_URL = "Repository.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceID)
     *
     * @var string
     */
    const SERVICE_RESOURCEID = "Service.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_RESOURCENAME = "Service.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_RELEASEDATE = "Service.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.Description)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_DESCRIPTION = "Service.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_CONTACT_PERSONID = "Service.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_CONTACT_ROLE = "Service.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_INFORMATIONURL_URL = "Service.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Service.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const SERVICE_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Service.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Service.AccessURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Service.AccessURL.URL)
     *
     * @var string
     */
    const SERVICE_ACCESSURL_URL = "Service.AccessURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceID)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEID = "Annotation.ResourceID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.ResourceName)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.ResourceName)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_RESOURCENAME = "Annotation.ResourceHeader.ResourceName";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.ReleaseDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.ReleaseDate)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_RELEASEDATE = "Annotation.ResourceHeader.ReleaseDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.Description)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.Description)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_DESCRIPTION = "Annotation.ResourceHeader.Description";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.Contact.PersonID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.Contact.PersonID)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_CONTACT_PERSONID = "Annotation.ResourceHeader.Contact.PersonID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.Contact.Role)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.Contact.Role)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_CONTACT_ROLE = "Annotation.ResourceHeader.Contact.Role";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.InformationURL.URL)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.InformationURL.URL)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_INFORMATIONURL_URL = "Annotation.ResourceHeader.InformationURL.URL";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.Association.AssociationID)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.Association.AssociationID)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONID = "Annotation.ResourceHeader.Association.AssociationID";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ResourceHeader.Association.AssociationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ResourceHeader.Association.AssociationType)
     *
     * @var string
     */
    const ANNOTATION_RESOURCEHEADER_ASSOCIATION_ASSOCIATIONTYPE = "Annotation.ResourceHeader.Association.AssociationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.AnnotationType)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.AnnotationType)
     *
     * @var string
     */
    const ANNOTATION_ANNOTATIONTYPE = "Annotation.AnnotationType";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.TimeSpan.StartDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.TimeSpan.StartDate)
     *
     * @var string
     */
    const ANNOTATION_TIMESPAN_STARTDATE = "Annotation.TimeSpan.StartDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.TimeSpan.StopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.TimeSpan.StopDate)
     *
     * @var string
     */
    const ANNOTATION_TIMESPAN_STOPDATE = "Annotation.TimeSpan.StopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.TimeSpan.RelativeStopDate)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.TimeSpan.RelativeStopDate)
     *
     * @var string
     */
    const ANNOTATION_TIMESPAN_RELATIVESTOPDATE = "Annotation.TimeSpan.RelativeStopDate";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ObservationExtent.StartLocation)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ObservationExtent.StartLocation)
     *
     * @var string
     */
    const ANNOTATION_OBSERVATIONEXTENT_STARTLOCATION = "Annotation.ObservationExtent.StartLocation";
    /**
     * Item Type - SPASE mapping choice candidate of the mapping setting screen(Annotation.ObservationExtent.StopLocation)
     * アイテムタイプ-マッピング設定画面のSPASEマッピング選択肢候補(Annotation.ObservationExtent.StopLocation)
     *
     * @var string
     */
    const ANNOTATION_OBSERVATIONEXTENT_STOPLOCATION = "Annotation.ObservationExtent.StopLocation";
}
?>