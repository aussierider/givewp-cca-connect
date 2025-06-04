import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Users } from "lucide-react";

interface DonorInfo {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  pincode: string;
  panNumber: string;
}

interface DonorInformationProps {
  donorInfo: DonorInfo;
  onDonorInfoChange: (info: DonorInfo) => void;
}

const DonorInformation = ({ donorInfo, onDonorInfoChange }: DonorInformationProps) => {
  const handleInputChange = (field: keyof DonorInfo, value: string) => {
    onDonorInfoChange({
      ...donorInfo,
      [field]: value
    });
  };

  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold flex items-center gap-2">
        <Users className="h-5 w-5 text-blue-500" />
        Donor Information
      </Label>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <Label htmlFor="firstName">First Name *</Label>
          <Input
            id="firstName"
            value={donorInfo.firstName}
            onChange={(e) => handleInputChange('firstName', e.target.value)}
            required
          />
        </div>
        <div>
          <Label htmlFor="lastName">Last Name *</Label>
          <Input
            id="lastName"
            value={donorInfo.lastName}
            onChange={(e) => handleInputChange('lastName', e.target.value)}
            required
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <Label htmlFor="email">Email Address *</Label>
          <Input
            id="email"
            type="email"
            value={donorInfo.email}
            onChange={(e) => handleInputChange('email', e.target.value)}
            required
          />
        </div>
        <div>
          <Label htmlFor="phone">Phone Number</Label>
          <Input
            id="phone"
            type="tel"
            value={donorInfo.phone}
            onChange={(e) => handleInputChange('phone', e.target.value)}
          />
        </div>
      </div>

      <div>
        <Label htmlFor="address">Address (for tax exemption certificate)</Label>
        <Input
          id="address"
          value={donorInfo.address}
          onChange={(e) => handleInputChange('address', e.target.value)}
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <Label htmlFor="city">City</Label>
          <Input
            id="city"
            value={donorInfo.city}
            onChange={(e) => handleInputChange('city', e.target.value)}
          />
        </div>
        <div>
          <Label htmlFor="state">State</Label>
          <Input
            id="state"
            value={donorInfo.state}
            onChange={(e) => handleInputChange('state', e.target.value)}
          />
        </div>
        <div>
          <Label htmlFor="pincode">PIN Code</Label>
          <Input
            id="pincode"
            value={donorInfo.pincode}
            onChange={(e) => handleInputChange('pincode', e.target.value)}
          />
        </div>
      </div>

      <div>
        <Label htmlFor="panNumber">PAN Number (for tax exemption certificate)</Label>
        <Input
          id="panNumber"
          value={donorInfo.panNumber}
          onChange={(e) => handleInputChange('panNumber', e.target.value)}
          placeholder="(for tax exemption certificate)"
        />
      </div>
    </div>
  );
};

export default DonorInformation;
