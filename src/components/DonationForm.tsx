
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Card, CardContent } from "@/components/ui/card";
import { formatIndianCurrency } from "@/utils/currencyUtils";
import { Heart, Star, Users, Zap } from "lucide-react";

interface DonationFormProps {
  onSubmit: (data: any) => void;
}

const DonationForm = ({ onSubmit }: DonationFormProps) => {
  const [selectedAmount, setSelectedAmount] = useState<string>('1000');
  const [customAmount, setCustomAmount] = useState<string>('');
  const [donorInfo, setDonorInfo] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    pincode: '',
    panNumber: ''
  });

  const predefinedAmounts = [
    { value: '500', label: '₹500', description: 'Feed 5 children' },
    { value: '1000', label: '₹1,000', description: 'School supplies', popular: true },
    { value: '2500', label: '₹2,500', description: 'Medical aid' },
    { value: '5000', label: '₹5,000', description: 'Education support' },
  ];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const amount = selectedAmount === 'custom' ? customAmount : selectedAmount;
    
    onSubmit({
      amount: parseFloat(amount),
      currency: 'INR',
      donorInfo,
      timestamp: new Date().toISOString()
    });
  };

  const isFormValid = () => {
    const amount = selectedAmount === 'custom' ? customAmount : selectedAmount;
    return amount && parseFloat(amount) >= 100 && 
           donorInfo.firstName && donorInfo.lastName && 
           donorInfo.email;
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-8">
      {/* Amount Selection */}
      <div className="space-y-4">
        <Label className="text-lg font-semibold flex items-center gap-2">
          <Heart className="h-5 w-5 text-red-500" />
          Choose Donation Amount
        </Label>
        
        <RadioGroup value={selectedAmount} onValueChange={setSelectedAmount}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {predefinedAmounts.map((amount) => (
              <div key={amount.value} className="relative">
                <RadioGroupItem value={amount.value} id={amount.value} className="sr-only" />
                <Label
                  htmlFor={amount.value}
                  className={`block p-4 rounded-lg border-2 cursor-pointer transition-all hover:shadow-md ${
                    selectedAmount === amount.value
                      ? 'border-blue-500 bg-blue-50 shadow-md'
                      : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex justify-between items-center">
                    <div>
                      <div className="font-bold text-lg">{amount.label}</div>
                      <div className="text-sm text-gray-600">{amount.description}</div>
                    </div>
                    {amount.popular && (
                      <Star className="h-5 w-5 text-yellow-500 fill-current" />
                    )}
                  </div>
                </Label>
              </div>
            ))}
          </div>

          {/* Custom Amount */}
          <Card className="border-dashed">
            <CardContent className="p-4">
              <div className="flex items-center space-x-4">
                <RadioGroupItem value="custom" id="custom" />
                <Label htmlFor="custom" className="flex-1">
                  <div className="flex items-center gap-2">
                    <Zap className="h-4 w-4 text-orange-500" />
                    Custom Amount
                  </div>
                </Label>
                <div className="flex-1">
                  <Input
                    type="number"
                    placeholder="Enter amount (min ₹100)"
                    value={customAmount}
                    onChange={(e) => setCustomAmount(e.target.value)}
                    disabled={selectedAmount !== 'custom'}
                    min="100"
                    className="text-right"
                  />
                </div>
              </div>
            </CardContent>
          </Card>
        </RadioGroup>

        {/* Amount Display */}
        <div className="text-center p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg">
          <div className="text-2xl font-bold text-gray-800">
            Total: {formatIndianCurrency(
              parseFloat(selectedAmount === 'custom' ? customAmount || '0' : selectedAmount)
            )}
          </div>
          <div className="text-sm text-gray-600">Amount inclusive of all charges</div>
        </div>
      </div>

      {/* Donor Information */}
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
              onChange={(e) => setDonorInfo({...donorInfo, firstName: e.target.value})}
              required
            />
          </div>
          <div>
            <Label htmlFor="lastName">Last Name *</Label>
            <Input
              id="lastName"
              value={donorInfo.lastName}
              onChange={(e) => setDonorInfo({...donorInfo, lastName: e.target.value})}
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
              onChange={(e) => setDonorInfo({...donorInfo, email: e.target.value})}
              required
            />
          </div>
          <div>
            <Label htmlFor="phone">Phone Number</Label>
            <Input
              id="phone"
              type="tel"
              value={donorInfo.phone}
              onChange={(e) => setDonorInfo({...donorInfo, phone: e.target.value})}
            />
          </div>
        </div>

        <div>
          <Label htmlFor="address">Address (for tax exemption certificate)</Label>
          <Input
            id="address"
            value={donorInfo.address}
            onChange={(e) => setDonorInfo({...donorInfo, address: e.target.value})}
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <Label htmlFor="city">City</Label>
            <Input
              id="city"
              value={donorInfo.city}
              onChange={(e) => setDonorInfo({...donorInfo, city: e.target.value})}
            />
          </div>
          <div>
            <Label htmlFor="state">State</Label>
            <Input
              id="state"
              value={donorInfo.state}
              onChange={(e) => setDonorInfo({...donorInfo, state: e.target.value})}
            />
          </div>
          <div>
            <Label htmlFor="pincode">PIN Code</Label>
            <Input
              id="pincode"
              value={donorInfo.pincode}
              onChange={(e) => setDonorInfo({...donorInfo, pincode: e.target.value})}
            />
          </div>
        </div>

        <div>
          <Label htmlFor="panNumber">PAN Number (for 80G receipt)</Label>
          <Input
            id="panNumber"
            value={donorInfo.panNumber}
            onChange={(e) => setDonorInfo({...donorInfo, panNumber: e.target.value})}
            placeholder="Optional - for tax exemption certificate"
          />
        </div>
      </div>

      {/* Submit Button */}
      <Button 
        type="submit" 
        className="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-lg py-6"
        disabled={!isFormValid()}
      >
        Proceed to CCAvenue Payment →
      </Button>
    </form>
  );
};

export default DonationForm;
