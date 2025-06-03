
import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import DonationForm from "./DonationForm";
import CCAvenuePlatform from "./CCAvenuePlatform";
import { Heart, Shield, Users } from "lucide-react";

const DonationInterface = () => {
  const [currentStep, setCurrentStep] = useState<'donation' | 'payment'>('donation');
  const [donationData, setDonationData] = useState<any>(null);

  const handleDonationSubmit = (data: any) => {
    setDonationData(data);
    setCurrentStep('payment');
  };

  const handleBackToDonation = () => {
    setCurrentStep('donation');
  };

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Header */}
      <div className="text-center mb-12">
        <div className="flex justify-center items-center gap-2 mb-4">
          <Heart className="h-8 w-8 text-red-500" />
          <h1 className="text-4xl font-bold text-gray-800">GiveWP × CCAvenue</h1>
        </div>
        <p className="text-xl text-gray-600 mb-6">
          Secure Indian Payment Gateway Integration for WordPress Donations
        </p>
        
        {/* Trust Indicators */}
        <div className="flex justify-center gap-6 mb-8">
          <div className="flex items-center gap-2">
            <Shield className="h-5 w-5 text-green-600" />
            <span className="text-sm text-gray-600">SSL Secured</span>
          </div>
          <div className="flex items-center gap-2">
            <Users className="h-5 w-5 text-blue-600" />
            <span className="text-sm text-gray-600">Trusted by 1000+ NGOs</span>
          </div>
          <Badge variant="outline" className="bg-orange-50 text-orange-700 border-orange-200">
            🇮🇳 India Focused
          </Badge>
        </div>
      </div>

      {/* Progress Indicator */}
      <div className="flex justify-center mb-8">
        <div className="flex items-center space-x-4">
          <div className={`flex items-center justify-center w-8 h-8 rounded-full ${
            currentStep === 'donation' ? 'bg-blue-600 text-white' : 'bg-green-600 text-white'
          }`}>
            1
          </div>
          <div className={`h-0.5 w-12 ${currentStep === 'payment' ? 'bg-green-600' : 'bg-gray-300'}`}></div>
          <div className={`flex items-center justify-center w-8 h-8 rounded-full ${
            currentStep === 'payment' ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'
          }`}>
            2
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-4xl mx-auto">
        {currentStep === 'donation' ? (
          <Card className="shadow-lg border-0 bg-white/80 backdrop-blur">
            <CardHeader className="text-center bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-t-lg">
              <CardTitle className="text-2xl">Make a Donation</CardTitle>
              <p className="opacity-90">Every contribution makes a difference</p>
            </CardHeader>
            <CardContent className="p-8">
              <DonationForm onSubmit={handleDonationSubmit} />
            </CardContent>
          </Card>
        ) : (
          <CCAvenuePlatform 
            donationData={donationData} 
            onBack={handleBackToDonation}
          />
        )}
      </div>

      {/* Footer Info */}
      <div className="mt-16 text-center text-sm text-gray-500">
        <p>This is a demonstration of GiveWP Add-on for CCAvenue Payment Gateway</p>
        <p className="mt-1">Supporting Indian NGOs with local payment methods</p>
      </div>
    </div>
  );
};

export default DonationInterface;
