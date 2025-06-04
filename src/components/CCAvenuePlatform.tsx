
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { formatIndianCurrency } from "@/utils/currencyUtils";
import { 
  ArrowLeft, 
  CreditCard, 
  Building2, 
  Smartphone, 
  Wallet,
  Shield,
  CheckCircle,
  Clock,
  AlertCircle
} from "lucide-react";

interface CCAvenuePlatformProps {
  donationData: any;
  onBack: () => void;
}

const CCAvenuePlatform = ({ donationData, onBack }: CCAvenuePlatformProps) => {
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<string>('');
  const [paymentStatus, setPaymentStatus] = useState<'selecting' | 'processing' | 'success' | 'failed'>('selecting');

  const paymentMethods = [
    {
      id: 'netbanking',
      name: 'Net Banking',
      icon: Building2,
      description: 'All major Indian banks supported',
      popular: true
    },
    {
      id: 'creditcard',
      name: 'Credit Card',
      icon: CreditCard,
      description: 'Visa, MasterCard, RuPay, Amex'
    },
    {
      id: 'debitcard',
      name: 'Debit Card',
      icon: CreditCard,
      description: 'All Indian bank debit cards'
    },
    {
      id: 'upi',
      name: 'UPI',
      icon: Smartphone,
      description: 'Pay using any UPI app'
    },
    {
      id: 'wallets',
      name: 'Digital Wallets',
      icon: Wallet,
      description: 'Paytm, PhonePe, Google Pay'
    }
  ];

  const handlePaymentMethodSelect = (methodId: string) => {
    setSelectedPaymentMethod(methodId);
  };

  const handlePayment = () => {
    setPaymentStatus('processing');
    
    // Simulate payment processing
    setTimeout(() => {
      // Randomly succeed or fail for demo purposes
      const success = Math.random() > 0.2; // 80% success rate
      setPaymentStatus(success ? 'success' : 'failed');
    }, 3000);
  };

  const renderPaymentStatus = () => {
    switch (paymentStatus) {
      case 'processing':
        return (
          <Card className="text-center">
            <CardContent className="p-8">
              <Clock className="h-12 w-12 text-blue-500 mx-auto mb-4 animate-spin" />
              <h3 className="text-xl font-semibold mb-2">Processing Payment...</h3>
              <p className="text-gray-600">Please wait while we process your donation through CCAvenue</p>
              <div className="mt-4 w-full bg-gray-200 rounded-full h-2">
                <div className="bg-blue-600 h-2 rounded-full animate-pulse" style={{width: '70%'}}></div>
              </div>
            </CardContent>
          </Card>
        );
      
      case 'success':
        return (
          <Card className="text-center border-green-200">
            <CardContent className="p-8">
              <CheckCircle className="h-16 w-16 text-green-500 mx-auto mb-4" />
              <h3 className="text-2xl font-bold text-green-700 mb-2">Payment Successful!</h3>
              <p className="text-gray-600 mb-4">
                Thank you for your generous donation of {formatIndianCurrency(donationData.amount)}
              </p>
              <div className="bg-green-50 p-4 rounded-lg mb-4">
                <p className="text-sm text-green-700">
                  <strong>Transaction ID:</strong> CCA{Date.now()}
                </p>
                <p className="text-sm text-green-700">
                  <strong>Payment Method:</strong> {selectedPaymentMethod.toUpperCase()}
                </p>
              </div>
              <Button onClick={() => window.location.reload()} className="bg-green-600 hover:bg-green-700">
                Make Another Donation
              </Button>
            </CardContent>
          </Card>
        );
      
      case 'failed':
        return (
          <Card className="text-center border-red-200">
            <CardContent className="p-8">
              <AlertCircle className="h-16 w-16 text-red-500 mx-auto mb-4" />
              <h3 className="text-2xl font-bold text-red-700 mb-2">Payment Failed</h3>
              <p className="text-gray-600 mb-4">
                We encountered an issue processing your payment. Please try again.
              </p>
              <div className="space-y-2">
                <Button 
                  onClick={() => setPaymentStatus('selecting')} 
                  className="w-full bg-blue-600 hover:bg-blue-700"
                >
                  Try Different Payment Method
                </Button>
                <Button variant="outline" onClick={onBack} className="w-full">
                  Back to Donation Form
                </Button>
              </div>
            </CardContent>
          </Card>
        );
      
      default:
        return null;
    }
  };

  if (paymentStatus !== 'selecting') {
    return (
      <div className="max-w-2xl mx-auto">
        {renderPaymentStatus()}
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <Card className="bg-gradient-to-r from-orange-500 to-red-500 text-white">
        <CardHeader>
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <img 
                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iIzIwNTNBNSIvPgo8dGV4dCB4PSI1IiB5PSIyNSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0id2hpdGUiPkNDQTwvdGV4dD4KPC9zdmc+" 
                alt="CCAvenue" 
                className="w-10 h-10"
              />
              <div>
                <CardTitle className="text-xl">CCAvenue Payment Gateway</CardTitle>
                <p className="text-orange-100 text-sm">Secure Indian Payment Processing</p>
              </div>
            </div>
            <Badge variant="secondary" className="bg-white/20 text-white">
              <Shield className="h-3 w-3 mr-1" />
              SSL Secured
            </Badge>
          </div>
        </CardHeader>
      </Card>

      {/* Order Summary */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center justify-between">
            <span>Order Summary</span>
            <Button variant="ghost" size="sm" onClick={onBack}>
              <ArrowLeft className="h-4 w-4 mr-2" />
              Edit Donation
            </Button>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            <div className="flex justify-between">
              <span>Donation Amount:</span>
              <span className="font-semibold">{formatIndianCurrency(donationData.amount)}</span>
            </div>
            <div className="flex justify-between">
              <span>Processing Fee:</span>
              <span className="text-green-600">₹0 (Waived)</span>
            </div>
            <Separator />
            <div className="flex justify-between text-lg font-bold">
              <span>Total Amount:</span>
              <span>{formatIndianCurrency(donationData.amount)}</span>
            </div>
          </div>
          
          <div className="mt-4 p-3 bg-blue-50 rounded-lg">
            <p className="text-sm text-blue-700">
              <strong>Donor:</strong> {donationData.donorInfo.firstName} {donationData.donorInfo.lastName}
            </p>
            <p className="text-sm text-blue-700">
              <strong>Email:</strong> {donationData.donorInfo.email}
            </p>
            {donationData.donorInfo.address && (
              <p className="text-sm text-blue-700">
                <strong>Address:</strong> {donationData.donorInfo.address}
              </p>
            )}
            {donationData.donorInfo.panNumber && (
              <p className="text-sm text-blue-700">
                <strong>PAN Number:</strong> {donationData.donorInfo.panNumber}
              </p>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Payment Methods */}
      <Card>
        <CardHeader>
          <CardTitle>Choose Payment Method</CardTitle>
          <p className="text-sm text-gray-600">Select your preferred payment option</p>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {paymentMethods.map((method) => {
              const IconComponent = method.icon;
              return (
                <div
                  key={method.id}
                  className={`relative p-4 rounded-lg border-2 cursor-pointer transition-all hover:shadow-md ${
                    selectedPaymentMethod === method.id
                      ? 'border-orange-500 bg-orange-50 shadow-md'
                      : 'border-gray-200 hover:border-gray-300'
                  }`}
                  onClick={() => handlePaymentMethodSelect(method.id)}
                >
                  <div className="flex items-start space-x-3">
                    <IconComponent className="h-6 w-6 text-orange-600 mt-1" />
                    <div className="flex-1">
                      <div className="flex items-center gap-2">
                        <h3 className="font-semibold">{method.name}</h3>
                        {method.popular && (
                          <Badge variant="secondary" className="text-xs">Popular</Badge>
                        )}
                      </div>
                      <p className="text-sm text-gray-600">{method.description}</p>
                    </div>
                  </div>
                  {selectedPaymentMethod === method.id && (
                    <CheckCircle className="absolute top-2 right-2 h-5 w-5 text-orange-600" />
                  )}
                </div>
              );
            })}
          </div>

          {selectedPaymentMethod && (
            <div className="mt-6 p-4 bg-gray-50 rounded-lg">
              <h4 className="font-semibold mb-2">Payment Security Features:</h4>
              <ul className="text-sm text-gray-600 space-y-1">
                <li>• 256-bit SSL encryption</li>
                <li>• PCI DSS compliant processing</li>
                <li>• Real-time fraud detection</li>
                <li>• Secure tokenization of card data</li>
              </ul>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Proceed Button */}
      <Button 
        onClick={handlePayment}
        disabled={!selectedPaymentMethod}
        className="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-lg py-6"
      >
        Pay {formatIndianCurrency(donationData.amount)} Securely
      </Button>

      {/* Trust Indicators */}
      <div className="text-center text-xs text-gray-500 space-y-1">
        <p>Powered by CCAvenue - India's leading payment gateway</p>
        <p>Your payment information is encrypted and secure</p>
      </div>
    </div>
  );
};

export default CCAvenuePlatform;
