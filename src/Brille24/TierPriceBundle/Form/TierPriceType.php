<?php
/**
 * This file is part of the Brille24 tierprice plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\TierPriceBundle\Form;


use Brille24\TierPriceBundle\Entity\TierPrice;
use Brille24\TierPriceBundle\Form\Validator\DescendingPrices;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Core\Model\Channel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{
    NotBlank, Range
};
use Webmozart\Assert\Assert;

/**
 * Class TierPriceType
 *
 * Form type for the tier price entity
 *
 * @package Brille24\TierPriceBundle\Form
 */
class TierPriceType extends AbstractType
{

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        Assert::keyExists($options, 'currency');

        $builder->add('qty', NumberType::class, [
            'label'       => 'sylius.ui.amount',
            'required'    => true,
            'constraints' => [
                new Range([
                    'min'        => 0,
                    'groups'     => 'sylius',
                    'minMessage' => 'Quantity has to be positive',
                ]),
                new NotBlank(['groups' => 'sylius']),
            ],
        ]);

        $builder->add('price', MoneyType::class, [
            'label'    => 'sylius.ui.price',
            'required' => true,
            'currency' => $options['currency'],
        ]);

        $builder->add('channel', EntityType::class, [
            'attr'        => ['style' => 'display:none'],
            'class'       => Channel::class,
            'constraints' => [
                new NotBlank(['groups' => 'sylius']),
            ],
        ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Setting USD as fallback
        $resolver->setDefaults([
            'data_class' => TierPrice::class,
            'currency'   => 'USD',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'tier_price';
    }
}
