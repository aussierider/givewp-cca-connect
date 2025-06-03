
import { useState } from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Card, CardContent } from "@/components/ui/card";
import { formatIndianCurrency } from "@/utils/currencyUtils";
import { Heart, Star, Zap } from "lucide-react";

interface AmountSelectionProps {
  selectedAmount: string;
  customAmount: string;
  onAmountChange: (amount: string) => void;
  onCustomAmountChange: (amount: string) => void;
}

const AmountSelection = ({ 
  selectedAmount, 
  customAmount, 
  onAmountChange, 
  onCustomAmountChange 
}: AmountSelectionProps) => {
  const predefinedAmounts = [
    { value: '500', label: '₹500', description: 'Feed 5 children' },
    { value: '1000', label: '₹1,000', description: 'School supplies', popular: true },
    { value: '2500', label: '₹2,500', description: 'Medical aid' },
    { value: '5000', label: '₹5,000', description: 'Education support' },
  ];

  return (
    <div className="space-y-4">
      <Label className="text-lg font-semibold flex items-center gap-2">
        <Heart className="h-5 w-5 text-red-500" />
        Choose Donation Amount
      </Label>
      
      <RadioGroup value={selectedAmount} onValueChange={onAmountChange}>
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
                  onChange={(e) => onCustomAmountChange(e.target.value)}
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
  );
};

export default AmountSelection;
