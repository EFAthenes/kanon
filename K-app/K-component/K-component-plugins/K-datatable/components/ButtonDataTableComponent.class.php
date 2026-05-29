<?php
/*
 * @license AGPL-3.0
 * 
 * @copyright Copyright (c) 2026 EFA, Ecole française d'athènes, EFAthenes.
 *
 * @author Louis Mulot <louis.mulot@efa.gr>
 * 
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 * 
 */
declare(strict_types=1);
class ButtonDataTableComponent extends ButtonComponent
{
    public const int BEFORE =1;
    public const int AFTER =2;
    public const int REPLACE =3;
    protected bool $activeURLTag=false;
    protected bool $activeLabelTag=false;
    protected int $placementLabel=2;
    protected string $labelSeparator= " ";
    public function setDynamicLabel(string $labelTag) : void
    {
        if($this->activeLabelTag)
        {
            $this->label=match($this->placementLabel)
            {
                self::BEFORE => $labelTag.$this->labelSeparator.$this->label,
                self::REPLACE => $labelTag,
                default => $this->label.$this->labelSeparator.$labelTag
            };
        }
    }
    public function setDynamicURL(string $urlTag) : void
    {
        if($this->activeURLTag)
        {
            $this->url.=$urlTag;
        }
    }    
    public function activateURLTag() : void
    {
        $this->activeURLTag=true;
    }
    public function activateLabelTag(int $placement=self::AFTER) : void
    {
        $this->placementLabel=$placement;
        $this->activeLabelTag=true;
    }
    public function deactivateURLTag() : void
    {
        $this->activeURLTag=false;
    }
    public function deactivateLabelTag() : void
    {
        $this->activeLabelTag=false;
    }   
    
    public function getLabelSeparator(): string
    {
        return $this->labelSeparator;
    }

    public function setLabelSeparator(string $labelSeparator): void
    {
        $this->labelSeparator = $labelSeparator;
    }
}