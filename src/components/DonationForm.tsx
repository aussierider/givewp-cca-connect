
import { useState } from "react";
import { Button } from "@/components/ui/button";
import AmountSelection from "./AmountSelection";
import DonorInformation from "./DonorInformation";

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
      <AmountSelection
        selectedAmount={selectedAmount}
        customAmount={customAmount}
        onAmountChange={setSelectedAmount}
        onCustomAmountChange={setCustomAmount}
      />

      <DonorInformation
        donorInfo={donorInfo}
        onDonorInfoChange={setDonorInfo}
      />

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
